<?php

use App\Http\Controllers\Admin\SupervisionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\Inventory\RestockController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\ProfileController;
use App\Models\Business;
use App\Models\PartnerProfile;
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
    Route::middleware('can:manage-business-profile')->group(function () {
        Route::get('/business-profile', [BusinessProfileController::class, 'edit'])->name('business-profile.edit');
        Route::put('/business-profile', [BusinessProfileController::class, 'update'])->name('business-profile.update');
    });
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
        Route::get('/inventory', [RestockController::class, 'index'])->name('inventory.index');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::post('/products/{product}/archive', [ProductController::class, 'archive'])->name('products.archive');
        Route::post('/inventory/{variant}/restocks', [RestockController::class, 'store'])->name('inventory.restocks.store');
        Route::get('/business/{business}', [BusinessProfileController::class, 'show'])
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
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit_logs.index');

        // Manajemen Pengguna & Pembuatan Akun Pemda (KSA-38, KSA-39, KSA-40)
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users/pemda', [UserManagementController::class, 'storePemda'])->name('users.pemda.store');
        Route::patch('/users/{user}/status', [UserManagementController::class, 'updateStatus'])->name('users.status');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        // Panel Supervisi Entitas Sistem & Operasi Lintas Modul (KSA-148, KSA-149, KSA-150)
        Route::get('/supervision', [SupervisionController::class, 'index'])->name('supervision.index');
        Route::patch('/businesses/{business}/publication', [SupervisionController::class, 'toggleBusinessPublication'])
            ->name('businesses.publication');
        Route::post('/business-categories', [SupervisionController::class, 'storeCategory'])
            ->name('categories.store');
    });

require __DIR__.'/auth.php';
