<?php

use App\Http\Controllers\ProfileController;
use App\Models\Business;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function (Request $request) {
    return Inertia::render('Dashboard', [
        'role' => $request->user()->role,
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Grup Rute Modul Berdasarkan Role & Ownership (KSA-12, KSA-35)
|--------------------------------------------------------------------------
*/

// Modul UMKM (Hanya Role 'umkm')
Route::middleware(['auth', 'role:umkm'])
    ->prefix('umkm')
    ->name('umkm.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('Umkm/Dashboard'))->name('dashboard');
        Route::get('/business/{business}', fn (Business $business) => response()->json($business))
            ->can('view', 'business')
            ->name('business.show');
        Route::put('/business/{business}', fn (Business $business) => response()->json(['message' => 'Profil usaha berhasil diperbarui']))
            ->can('update', 'business')
            ->name('business.update');
        Route::delete('/business/{business}', fn (Business $business) => response()->json(['message' => 'Profil usaha berhasil dihapus']))
            ->can('delete', 'business')
            ->name('business.destroy');
    });

// Modul Mitra Industri (Hanya Role 'mitra')
Route::middleware(['auth', 'role:mitra'])
    ->prefix('mitra')
    ->name('mitra.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('Mitra/Dashboard'))->name('dashboard');
        Route::get('/profile/{partnerProfile}', fn (PartnerProfile $partnerProfile) => response()->json($partnerProfile))
            ->can('view', 'partnerProfile')
            ->name('profile.show');
        Route::put('/profile/{partnerProfile}', fn (PartnerProfile $partnerProfile) => response()->json(['message' => 'Profil organisasi mitra berhasil diperbarui']))
            ->can('update', 'partnerProfile')
            ->name('profile.update');
        Route::delete('/profile/{partnerProfile}', fn (PartnerProfile $partnerProfile) => response()->json(['message' => 'Profil organisasi mitra berhasil dihapus']))
            ->can('delete', 'partnerProfile')
            ->name('profile.destroy');
    });

// Modul Pemerintah Daerah (Hanya Role 'pemda')
Route::middleware(['auth', 'role:pemda'])
    ->prefix('pemda')
    ->name('pemda.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('Pemda/Dashboard'))->name('dashboard');
        Route::get('/monitoring', fn () => response()->json(['status' => 'monitoring_data_ok']))->name('monitoring');
        Route::get('/umkm/{business}', fn (Business $business) => response()->json($business))
            ->can('view', 'business')
            ->name('umkm.show');
    });

// Modul Superadmin (Hanya Role 'admin')
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');
        Route::get('/users', fn () => response()->json(['users' => User::all()]))
            ->can('viewAny', User::class)
            ->name('users.index');
        Route::patch('/users/{user}/status', function (User $user, Request $request) {
            $user->update(['account_status' => $request->input('account_status', User::STATUS_DISABLED)]);

            return response()->json(['message' => 'Status akun pengguna diperbarui', 'user' => $user]);
        })->can('manageStatus', 'user')->name('users.status');
        Route::delete('/users/{user}', function (User $user) {
            $user->delete();

            return response()->json(['message' => 'Pengguna berhasil dihapus']);
        })->can('delete', 'user')->name('users.destroy');
    });

require __DIR__.'/auth.php';
