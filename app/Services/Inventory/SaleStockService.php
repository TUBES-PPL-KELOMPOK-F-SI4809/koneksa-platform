<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleStockService
{
    /** @param array<int, array{product_variant_id: int, quantity: int}> $items */
    public function record(User $actor, array $items, ?string $notes = null): Sale
    {
        Gate::forUser($actor)->authorize('create', Product::class);
        $ids = array_map(fn (array $item): int => (int) $item['product_variant_id'], $items);
        if (count($items) < 1 || count($items) > 50 || count(array_unique($ids)) !== count($ids)) {
            throw ValidationException::withMessages(['items' => 'Pilih 1–50 varian berbeda untuk transaksi.']);
        }

        return DB::transaction(function () use ($actor, $items, $ids, $notes): Sale {
            // Keep the same product -> variant lock order as catalog updates and restock.
            $productIds = ProductVariant::whereIn('id', $ids)->pluck('product_id')->unique();
            $products = Product::with('business')->whereIn('id', $productIds)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $variants = ProductVariant::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            abort_unless($variants->count() === count($ids), 404);
            $totalCents = 0;
            foreach ($items as $index => $item) {
                $variant = $variants->get((int) $item['product_variant_id']);
                $product = $products->get($variant->product_id);
                abort_unless($product, 404);
                $variant->setRelation('product', $product);
                Gate::forUser($actor)->authorize('sell', $variant);
                $quantity = filter_var($item['quantity'], FILTER_VALIDATE_INT);
                if ($quantity === false || $quantity < 1 || $quantity > $variant->stock_quantity) {
                    throw ValidationException::withMessages(["items.$index.quantity" => 'Jumlah penjualan harus positif dan tidak boleh melebihi stok tersedia.']);
                }
                $priceCents = (int) str_replace('.', '', $variant->selling_price);
                if ($priceCents < 0 || ($priceCents > 0 && $quantity > intdiv(999999999999999 - $totalCents, $priceCents))) {
                    throw ValidationException::withMessages(['items' => 'Total penjualan melebihi batas yang dapat disimpan.']);
                }
                $totalCents += $priceCents * $quantity;
            }

            $sale = Sale::create([
                'business_id' => $actor->business()->firstOrFail()->id,
                'invoice_number' => 'INV-'.Str::ulid(), 'sold_at' => now(), 'status' => 'recorded',
                'total_amount' => sprintf('%d.%02d', intdiv($totalCents, 100), $totalCents % 100), 'notes' => $notes,
            ]);
            foreach ($items as $item) {
                $variant = $variants->get((int) $item['product_variant_id']);
                $quantity = (int) $item['quantity'];
                $sale->items()->create([
                    'product_variant_id' => $variant->id, 'product_name_snapshot' => $variant->product->name,
                    'variant_label_snapshot' => $variant->variant_key, 'unit_snapshot' => $variant->product->unit,
                    'quantity' => $quantity, 'unit_price' => $variant->selling_price,
                ]);
                $variant->update(['stock_quantity' => $variant->stock_quantity - $quantity]);
                $variant->stockMovements()->create([
                    'sale_id' => $sale->id, 'performed_by' => $actor->id,
                    'type' => 'sale', 'quantity_delta' => -$quantity, 'notes' => $notes,
                ]);
            }

            return $sale->load('items');
        }, attempts: 3);
    }
}
