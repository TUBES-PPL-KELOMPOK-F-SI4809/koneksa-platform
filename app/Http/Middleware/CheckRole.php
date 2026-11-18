<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Pastikan pengguna terautentikasi
        if (! $user) {
            if ($request->expectsJson() || $request->is('api/*')) {
                abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
            }

            return redirect()->guest(route('login'));
        }

        // 2. Pastikan akun dalam status aktif (account_status === 'enabled')
        if ($user->account_status !== User::STATUS_ENABLED || $user->trashed()) {
            Auth::guard('web')->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            if ($request->expectsJson() || $request->is('api/*')) {
                abort(Response::HTTP_FORBIDDEN, 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            return redirect()->route('login')->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.',
            ]);
        }

        // 3. Jika tidak ada spesifikasi role, loloskan pengguna aktif
        if (empty($roles)) {
            return $next($request);
        }

        // 4. Normalisasi variadic roles (mendukung koma, pipe, atau multi-arg)
        $allowedRoles = [];
        foreach ($roles as $roleParam) {
            foreach (explode('|', $roleParam) as $subRole) {
                foreach (explode(',', $subRole) as $cleanRole) {
                    $trimmed = trim($cleanRole);
                    if ($trimmed !== '') {
                        $allowedRoles[] = $trimmed;
                    }
                }
            }
        }

        // 5. Periksa otorisasi role
        if (! in_array($user->role, $allowedRoles, true)) {
            abort(Response::HTTP_FORBIDDEN, 'Akses ditolak: role Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return $next($request);
    }
}
