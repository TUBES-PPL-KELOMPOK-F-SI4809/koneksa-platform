<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class ProductService
{
    /**
     * @param array{name: string, unit: string, description?: ?string, variants: array<int, array{
     *     id?: ?int, variant_key: string, selling_price: string|int|float, low_stock_threshold: int|string,
     *     sku?: ?string, size?: ?string, color?: ?string
     * }>} $data
     */
    public function save(User $actor, array $data, ?Product $product = null): Product
    {
        return DB::transaction(function () use ($actor, $data, $product) {
            if ($product) {
                $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
                Gate::forUser($actor)->authorize('update', $product);
                if ($product->archived_at !== null) {
                    throw ValidationException::withMessages(['product' => 'Produk yang diarsipkan tidak dapat diubah.']);
                }
            } else {
                Gate::forUser($actor)->authorize('create', Product::class);
                $product = new Product(['business_id' => $actor->business->id]);
            }

            $existing = $product->exists ? $product->variants()->orderBy('id')->lockForUpdate()->get()->keyBy('id') : collect();
            $submittedIds = collect($data['variants'])->pluck('id')->filter();
            // Preserve every existing variant, including its stock and historical references.
            if ($existing->keys()->diff($submittedIds)->isNotEmpty() || $submittedIds->diff($existing->keys())->isNotEmpty()) {
                throw ValidationException::withMessages(['variants' => 'Daftar varian berubah atau tidak sesuai produk. Muat ulang halaman sebelum menyimpan.']);
            }

            $product->fill(Arr::only($data, ['name', 'description', 'unit']))->save();
            foreach ($data['variants'] as $index => $row) {
                $variant = isset($row['id']) ? $existing->get($row['id']) : $product->variants()->make();
                if ($variant->exists && $variant->variant_key !== $row['variant_key']) {
                    throw ValidationException::withMessages(["variants.$index.variant_key" => 'Nama varian yang sudah tersimpan tidak dapat diubah.']);
                }
                $variant->fill(Arr::only($row, ['variant_key', 'sku', 'size', 'color', 'selling_price', 'low_stock_threshold']))->save();
            }

            return $product;
        }, attempts: 3);
    }

    public function archive(User $actor, Product $product): void
    {
        DB::transaction(function () use ($actor, $product) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            Gate::forUser($actor)->authorize('archive', $locked);
            if ($locked->archived_at === null) {
                $locked->update(['archived_at' => now()]);
            }
        }, attempts: 3);
    }
}
