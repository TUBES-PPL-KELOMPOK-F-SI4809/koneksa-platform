<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'business_category_id',
    'name',
    'description',
    'address',
    'district',
    'subdistrict',
    'contact_phone',
    'logo_path',
    'business_needs',
    'published_at',
])]
class Business extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
        ];
    }

    /**
     * Akun pengguna pemilik usaha.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Kategori usaha.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BusinessCategory::class, 'business_category_id');
    }

    /**
     * Katalog produk milik usaha.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Riwayat transaksi penjualan usaha.
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Riwayat pengajuan verifikasi administratif ke Pemda.
     */
    public function verificationRequests(): HasMany
    {
        return $this->hasMany(VerificationRequest::class);
    }

    /**
     * Riwayat proposal kolaborasi bisnis (FR-12).
     */
    public function collaborationProposals(): HasMany
    {
        return $this->hasMany(CollaborationProposal::class);
    }

    /**
     * Tentukan apakah usaha telah terverifikasi oleh Pemda.
     */
    public function isVerified(): bool
    {
        return $this->verificationRequests()->where('status', 'approved')->exists();
    }
}
