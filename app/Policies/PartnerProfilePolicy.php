<?php

namespace App\Policies;

use App\Models\PartnerProfile;
use App\Models\User;

class PartnerProfilePolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar mitra industri.
     */
    public function viewAny(User $user): bool
    {
        return $user->isEnabled();
    }

    /**
 * Mitra aktif, terverifikasi, dan tidak wajib ganti password
 * boleh mengelola profil organisasinya (FR-16).
 */
public function manage(User $user): bool
{
    return $user->isMitra()
        && $user->isEnabled()
        && $user->email_verified_at !== null
        && ! $user->must_change_password;
}

    /**
     * Tentukan apakah pengguna dapat melihat profil mitra industri spesifik.
     */
    public function view(User $user, PartnerProfile $partnerProfile): bool
    {
        // 1. Pemilik organisasi mitra selalu dapat melihat miliknya
        if ($partnerProfile->user_id === $user->id) {
            return true;
        }

        // 2. Semua role aktif dapat melihat direktori mitra
        return $user->isEnabled();
    }

    /**
     * Tentukan apakah pengguna dapat membuat profil mitra industri baru.
     * Hanya role Mitra yang belum memiliki profil yang diizinkan (1-to-1).
     */
    public function create(User $user): bool
    {
        return $user->isMitra() && $user->partnerProfile === null;
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui profil mitra industri.
     * Hanya pemilik organisasi mitra bersangkutan yang berhak mengubah.
     */
    public function update(User $user, PartnerProfile $partnerProfile): bool
    {
        return $user->isMitra() && $partnerProfile->user_id === $user->id;
    }

    /**
     * Tentukan apakah pengguna dapat menghapus profil mitra industri.
     * Hanya pemilik organisasi mitra bersangkutan yang berhak menghapus.
     */
    public function delete(User $user, PartnerProfile $partnerProfile): bool
    {
        return $user->isMitra() && $partnerProfile->user_id === $user->id;
    }
}
