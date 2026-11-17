<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Tentukan apakah pengguna dapat melihat daftar seluruh user (Manajemen Pengguna).
     * Hanya Superadmin yang berhak.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Tentukan apakah pengguna dapat melihat detail akun pengguna lain.
     */
    public function view(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Tentukan apakah pengguna dapat memperbarui profil akun.
     */
    public function update(User $user, User $model): bool
    {
        return $user->isAdmin() || $user->id === $model->id;
    }

    /**
     * Tentukan apakah pengguna dapat mengelola status administratif akun (aktifkan/nonaktifkan/ganti role).
     * Hanya Superadmin, dan dilarang menonaktifkan akun sendiri.
     */
    public function manageStatus(User $user, User $model): bool
    {
        return $user->isAdmin() && $user->id !== $model->id;
    }

    /**
     * Tentukan apakah pengguna dapat membuat akun Pemda khusus.
     */
    public function createPemda(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Tentukan apakah pengguna dapat menghapus akun.
     * Pengguna biasa dapat menghapus akun sendiri (self-deletion).
     * Superadmin dapat menghapus akun pengguna lain, tetapi dilarang menghapus akunnya sendiri.
     */
    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return ! $user->isAdmin();
        }

        return $user->isAdmin();
    }
}
