<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
])]
class BusinessCategory extends Model
{
    use HasFactory;

    /**
     * Daftar usaha dalam kategori ini.
     */
    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
