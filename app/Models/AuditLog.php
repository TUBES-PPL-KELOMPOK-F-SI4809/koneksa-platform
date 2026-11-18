<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'actor_id',
        'actor_role',
        'module',
        'action',
        'auditable_type',
        'auditable_id',
        'description',
        'metadata',
        'ip_address',
        'created_at',
    ];

    public const MODULE_AUTH = 'auth';

    public const MODULE_USER = 'user';

    public const MODULE_SUPERADMIN = 'superadmin';

    public const MODULE_VERIFICATION = 'verification';

    public const MODULE_PROGRAM = 'program';

    public const MODULE_COLLABORATION = 'collaboration';

    public const MODULES = [
        self::MODULE_AUTH,
        self::MODULE_USER,
        self::MODULE_SUPERADMIN,
        self::MODULE_VERIFICATION,
        self::MODULE_PROGRAM,
        self::MODULE_COLLABORATION,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke actor pelaksana aksi (User).
     */
    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Relasi polimorfik ke entitas auditable target.
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope filter pencarian audit trail (KSA-155 / FR-32.2).
     *
     * @param  Builder<AuditLog>  $query
     * @param  array<string, mixed>  $filters
     */
    public function scopeFilter(Builder $query, array $filters): void
    {
        $query->when($filters['module'] ?? null, function (Builder $query, string $module) {
            $query->where('module', $module);
        });

        $query->when($filters['action'] ?? null, function (Builder $query, string $action) {
            $query->where('action', 'like', "%{$action}%");
        });

        $query->when($filters['actor_role'] ?? null, function (Builder $query, string $role) {
            $query->where('actor_role', $role);
        });

        $query->when($filters['date_from'] ?? null, function (Builder $query, string $dateFrom) {
            $query->where('created_at', '>=', "{$dateFrom} 00:00:00");
        });

        $query->when($filters['date_to'] ?? null, function (Builder $query, string $dateTo) {
            $query->where('created_at', '<=', "{$dateTo} 23:59:59");
        });

        $query->when($filters['search'] ?? null, function (Builder $query, string $search) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('ip_address', 'like', "%{$search}%")
                    ->orWhereHas('actor', function (Builder $actorQuery) use ($search) {
                        $actorQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        });
    }
}
