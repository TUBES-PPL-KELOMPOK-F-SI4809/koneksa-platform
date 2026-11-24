<?php

namespace App\Policies;

use App\Models\CollaborationProposal;
use App\Models\User;

class CollaborationProposalPolicy
{
    /**
     * Tentukan apakah user dapat melihat daftar proposal.
     */
    public function viewAny(User $user): bool
    {
        return $user->isEnabled();
    }

    /**
     * Tentukan apakah user dapat melihat rincian proposal tertentu.
     */
    public function view(User $user, CollaborationProposal $proposal): bool
    {
        if (! $user->isEnabled()) {
            return false;
        }

        // Admin dan Pemda dapat melihat untuk audit & monitoring kemitraan
        if ($user->isAdmin() || $user->isPemda()) {
            return true;
        }

        // Pihak yang terlibat (UMKM atau Mitra pemilik)
        return $proposal->isParticipant($user);
    }

    /**
     * Tentukan apakah user dapat mengajukan proposal kolaborasi baru (FR-12.1).
     */
    public function create(User $user): bool
    {
        if (! $user->isEnabled()) {
            return false;
        }

        if ($user->isUmkm()) {
            return $user->business !== null;
        }

        if ($user->isMitra()) {
            return $user->partnerProfile !== null;
        }

        return false;
    }

    /**
     * Tentukan apakah user berhak merespon (terima/tolak) proposal (FR-12.2 / BR-05).
     * Aturan BR-05: Hanya pihak penerima (lawan dari pengaju) yang berhak menerima/menolak.
     */
    public function respond(User $user, CollaborationProposal $proposal): bool
    {
        if (! $user->isEnabled() || ! $proposal->isPending()) {
            return false;
        }

        // Inisiator dilarang merespon proposalnya sendiri
        if ($proposal->initiated_by === $user->id) {
            return false;
        }

        return $proposal->isRecipient($user);
    }
}

