<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * Kategori entitas bisnis yang dapat dipilih Mitra (FR-16.1).
     */
    public const ENTITY_CATEGORIES = [
        'Teknologi Informasi & Telekomunikasi',
        'Perbankan & Jasa Keuangan',
        'Manufaktur & Industri',
        'Perdagangan & Distribusi',
        'Ritel & E-commerce',
        'Pariwisata & Perhotelan',
        'Pendidikan & Pelatihan',
        'Konsultan & Jasa Profesional',
        'Lainnya',
    ];

    /**
     * Akun pengguna pemilik profil mitra.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jenis kerja sama yang ditawarkan mitra (FR-16.2).
     */
    public function cooperationTypes(): BelongsToMany
    {
        return $this->belongsToMany(CooperationType::class, 'partner_cooperation_types')
            ->withTimestamps();
    }

    /**
     * Riwayat proposal kolaborasi mitra (FR-12).
     */
    public function collaborationProposals(): HasMany
    {
        return $this->hasMany(CollaborationProposal::class);
    }
}