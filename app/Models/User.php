<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'email',
    'password',
    'role',
    'phone',
    'avatar_path',
    'account_status',
    'must_change_password',
    'current_session_id',
])]
#[Hidden([
    'password',
    'remember_token',
    'current_session_id',
])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const ROLE_UMKM = 'umkm';

    public const ROLE_MITRA = 'mitra';

    public const ROLE_PEMDA = 'pemda';

    public const ROLE_ADMIN = 'admin';

    public const ROLES = [
        self::ROLE_UMKM,
        self::ROLE_MITRA,
        self::ROLE_PEMDA,
        self::ROLE_ADMIN,
    ];

    public const PUBLIC_ROLES = [
        self::ROLE_UMKM,
        self::ROLE_MITRA,
    ];

    public const STATUS_ENABLED = 'enabled';

    public const STATUS_DISABLED = 'disabled';

    public const ACCOUNT_STATUSES = [
        self::STATUS_ENABLED,
        self::STATUS_DISABLED,
    ];

    /**
     * The model's default attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'role' => self::ROLE_UMKM,
        'account_status' => self::STATUS_ENABLED,
        'must_change_password' => false,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Profil usaha UMKM milik user.
     */
    public function business(): HasOne
    {
        return $this->hasOne(Business::class);
    }

    /**
     * Profil organisasi Mitra Industri milik user.
     */
    public function partnerProfile(): HasOne
    {
        return $this->hasOne(PartnerProfile::class);
    }

    public function isUmkm(): bool
    {
        return $this->role === self::ROLE_UMKM;
    }

    public function isMitra(): bool
    {
        return $this->role === self::ROLE_MITRA;
    }

    public function isPemda(): bool
    {
        return $this->role === self::ROLE_PEMDA;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    /**
     * Periksa apakah pengguna memiliki salah satu role yang diberikan.
     */
    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles, true);
        }

        return $this->role === $roles;
    }

    public function isEnabled(): bool
    {
        return $this->account_status === self::STATUS_ENABLED;
    }

    public function isDisabled(): bool
    {
        return $this->account_status === self::STATUS_DISABLED;
    }

    /**
     * Scope query untuk pengguna aktif (account_status = enabled).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('account_status', self::STATUS_ENABLED);
    }

    /**
     * Scope query berdasarkan role tertentu.
     */
    public function scopeRole(Builder $query, string|array $roles): Builder
    {
        if (is_array($roles)) {
            return $query->whereIn('role', $roles);
        }

        return $query->where('role', $roles);
    }
}
