<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Daftar kunci sensitif yang wajib di-masking / dibersihkan dari audit metadata (BR-15).
     */
    protected const SENSITIVE_KEYS = [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'remember_token',
        'current_session_id',
        'token',
        'secret',
        'api_key',
    ];

    /**
     * Catat aktivitas penting ke audit trail secara append-only.
     *
     * @param  array<string, mixed>  $metadata
     */
    public function log(
        string $module,
        string $action,
        string $description,
        ?Model $auditable = null,
        array $metadata = [],
        ?User $actor = null,
        ?string $ipAddress = null
    ): AuditLog {
        $actor = $actor ?? Auth::user();
        $ipAddress = $ipAddress ?? request()?->ip();

        $sanitizedMetadata = $this->sanitizeMetadata($metadata);

        return AuditLog::create([
            'actor_id' => $actor?->id,
            'actor_role' => $actor?->role,
            'module' => $module,
            'action' => $action,
            'auditable_type' => $auditable ? $auditable->getMorphClass() : null,
            'auditable_id' => $auditable?->getKey(),
            'description' => $description,
            'metadata' => empty($sanitizedMetadata) ? null : $sanitizedMetadata,
            'ip_address' => $ipAddress,
            'created_at' => now(),
        ]);
    }

    /**
     * Membersihkan metadata secara rekursif dari kunci-kunci sensitif (password, token, secret).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sanitizeMetadata(array $data): array
    {
        $sanitized = [];

        foreach ($data as $key => $value) {
            $lowerKey = strtolower((string) $key);

            if (in_array($lowerKey, self::SENSITIVE_KEYS, true)) {
                $sanitized[$key] = '[REDACTED]';

                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeMetadata($value);
            } else {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }
}
