<?php

namespace App\Services\Inventory;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RestockService
{
    public function restock(ProductVariant $variant, User $actor, int $quantity, ?string $notes): StockMovement
    {
        return DB::transaction(function () use ($variant, $actor, $quantity, $notes) {
            $product = Product::whereKey($variant->product_id)->lockForUpdate()->firstOrFail();
            $lockedVariant = $product->variants()->whereKey($variant->id)->lockForUpdate()->firstOrFail();
            $lockedVariant->setRelation('product', $product);

            Gate::forUser($actor)->authorize('restock', $lockedVariant);

            if ($product->archived_at !== null || $lockedVariant->archived_at !== null) {
                throw ValidationException::withMessages([
                    'restock' => 'Produk atau varian yang diarsipkan tidak dapat direstock.',
                ]);
            }

            if ($quantity < 1 || $quantity > 2147483647 - $lockedVariant->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Jumlah restock harus positif dan saldo stok tidak boleh melebihi 2.147.483.647.',
                ]);
            }

            $lockedVariant->update(['stock_quantity' => $lockedVariant->stock_quantity + $quantity]);

            return $lockedVariant->stockMovements()->create([
                'performed_by' => $actor->id,
                'type' => 'restock',
                'quantity_delta' => $quantity,
                'notes' => $notes,
            ]);
        }, attempts: 3);
    }
}
