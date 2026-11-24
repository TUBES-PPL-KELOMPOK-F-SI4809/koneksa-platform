<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'business_id',
    'partner_profile_id',
    'initiated_by',
    'cooperation_type_id',
    'digital_offering_id',
    'title',
    'description',
    'attachment_path',
    'status',
    'response_note',
    'responded_at',
    'started_at',
    'completed_at',
])]
class CollaborationProposal extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_ONGOING = 'ongoing';

    public const STATUS_COMPLETED = 'completed';

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function partnerProfile(): BelongsTo
    {
        return $this->belongsTo(PartnerProfile::class);
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function cooperationType(): BelongsTo
    {
        return $this->belongsTo(CooperationType::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isAccepted(): bool
    {
        return $this->status === self::STATUS_ACCEPTED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    /**
     * Tentukan apakah user tertentu adalah penerima proposal (pihak lawan dari initiator).
     */
    public function isRecipient(User $user): bool
    {
        if ($this->initiated_by === $user->id) {
            return false;
        }

        // Jika diajukan oleh UMKM, penerima adalah Mitra pemilik partner_profile
        if ($this->business && $this->business->user_id === $this->initiated_by) {
            return $this->partnerProfile && $this->partnerProfile->user_id === $user->id;
        }

        // Jika diajukan oleh Mitra, penerima adalah UMKM pemilik business
        if ($this->partnerProfile && $this->partnerProfile->user_id === $this->initiated_by) {
            return $this->business && $this->business->user_id === $user->id;
        }

        return false;
    }

    /**
     * Tentukan apakah user terlibat dalam proposal (sebagai pemilik UMKM atau pemilik Mitra).
     */
    public function isParticipant(User $user): bool
    {
        return ($this->business && $this->business->user_id === $user->id)
            || ($this->partnerProfile && $this->partnerProfile->user_id === $user->id);
    }
}
