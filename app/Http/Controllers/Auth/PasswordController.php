<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     * Memverifikasi password lama, melakukan hashing password baru,
     * serta mereset flag must_change_password (FR-02.2, BR-01).
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        app(AuditLogService::class)->log(
            AuditLog::MODULE_AUTH,
            'user.password_changed',
            "Pengguna {$user->name} memperbarui kata sandi akun",
            $user
        );

        return back()->with('status', 'password-updated');
    }
}
