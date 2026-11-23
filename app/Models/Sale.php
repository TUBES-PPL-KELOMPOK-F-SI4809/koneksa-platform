<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['business_id', 'invoice_number', 'sold_at', 'status', 'total_amount', 'notes'])]
class Sale extends Model
{
    protected function casts(): array
    {
        return ['sold_at' => 'datetime', 'total_amount' => 'decimal:2'];
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}
