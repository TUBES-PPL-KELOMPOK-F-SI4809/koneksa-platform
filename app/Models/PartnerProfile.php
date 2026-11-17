<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'organization_name',
    'industry',
    'description',
    'address',
    'contact_phone',
    'website_url',
    'logo_path',
])]
class PartnerProfile extends Model
{
    use HasFactory;

    /**
     * Akun pengguna pemilik profil mitra.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
