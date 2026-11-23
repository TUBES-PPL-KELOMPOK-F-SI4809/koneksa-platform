<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;

class BusinessPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar usaha.
     */
    public function viewAny(User $user): bool
    {
        return $user->isEnabled();
    }

    /**
     * Tentukan apakah pengguna dapat melihat profil usaha spesifik.
     */
    public function view(User $user, Business $business): bool
    {
        // 1. Pemilik usaha selalu dapat melihat miliknya
        if ($business->user_id === $user->id) {
            return true;
        }

        // 2. Superadmin dan Pemda dapat melihat untuk audit & monitoring
        if ($user->isAdmin() || $user->isPemda()) {
            return true;
        }

        // 3. Pengguna lain hanya dapat melihat profil yang telah dipublikasikan
        return $business->published_at !== null && $business->user?->isEnabled() === true;
    }

    /**
     * Tentukan apakah pengguna dapat membuat profil usaha baru.
     * Hanya role UMKM yang belum memiliki profil usaha yang diizinkan (1-to-1).
     */
    public function create(User $user): bool
    {
        return $user->isUmkm() && $user->business === null;
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui profil usaha.
     * Hanya pemilik usaha (UMKM bersangkutan) yang berhak mengubah data usahanya.
     */
    public function update(User $user, Business $business): bool
    {
        return $user->isUmkm() && $business->user_id === $user->id;
    }

    /**
     * Tentukan apakah pengguna dapat menghapus profil usaha.
     * Hanya pemilik usaha (UMKM bersangkutan) yang berhak menghapus.
     */
    public function delete(User $user, Business $business): bool
    {
        return $user->isUmkm() && $business->user_id === $user->id;
    }

    /**
     * Tentukan apakah Superadmin dapat melakukan tindakan supervisi administratif lintas modul (KSA-148 / FR-31).
     */
    public function supervise(User $user, Business $business): bool
    {
        return $user->isAdmin();
    }
}
