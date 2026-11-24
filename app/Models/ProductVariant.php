<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['product_id', 'variant_key', 'sku', 'size', 'color', 'selling_price', 'stock_quantity', 'low_stock_threshold', 'archived_at'])]
class ProductVariant extends Model
{
    #[Scope]
    protected function availableForSale(Builder $query): void
    {
        $query->whereNull('archived_at')
            ->whereHas('product', fn (Builder $product) => $product->whereNull('archived_at'));
    }

    #[Scope]
    protected function lowStock(Builder $query): void
    {
        $query->availableForSale()->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    protected function stockStatus(): Attribute
    {
        return Attribute::get(fn (): string => match (true) {
            $this->archived_at !== null => 'archived',
            $this->stock_quantity === 0 => 'out',
            $this->stock_quantity <= $this->low_stock_threshold => 'low',
            default => 'healthy',
        });
    }

    protected function casts(): array
    {
        return [
            'selling_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'low_stock_threshold' => 'integer',
            'archived_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
