<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_variant_id', 'product_name_snapshot', 'variant_label_snapshot', 'unit_snapshot', 'quantity', 'unit_price'])]
class SaleItem extends Model
{
    protected function casts(): array
    {
        return ['quantity' => 'integer', 'unit_price' => 'decimal:2'];
    }
}
