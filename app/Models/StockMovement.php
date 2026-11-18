<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_variant_id', 'sale_id', 'performed_by', 'type', 'quantity_delta', 'notes', 'created_at'])]
class StockMovement extends Model
{
    public const UPDATED_AT = null;

    protected function casts(): array
    {
        return ['quantity_delta' => 'integer'];
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by')->withTrashed();
    }
}
