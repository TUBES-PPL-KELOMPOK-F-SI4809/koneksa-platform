<?php

use App\Http\Controllers\Admin\SupervisionController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BusinessProfileController;
use App\Http\Controllers\CollaborationProposalController;
use App\Http\Controllers\Inventory\RestockController;
use App\Http\Controllers\Inventory\StockDashboardController;
use App\Http\Controllers\Inventory\StockSaleController;
use App\Http\Controllers\PartnerProfile\PartnerProfileController;
use App\Http\Controllers\Product\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBusinessDirectoryController;
use App\Http\Controllers\PublicBusinessProfileController;
use App\Http\Controllers\Sale\SaleController;
use App\Http\Controllers\Sale\SalesReportController;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

Route::get('/', function () {
    $hasBusinessesTable = Schema::hasTable('businesses');

    $publishedCount = 0;
    $categoriesCount = 0;
    $featuredBusinesses = [];

    if ($hasBusinessesTable) {
        $publishedQuery = Business::query()
            ->whereNotNull('published_at')
            ->whereHas('user', fn ($q) => $q->where('account_status', User::STATUS_ENABLED));

        $publishedCount = (clone $publishedQuery)->count();
        $categoriesCount = Schema::hasTable('business_categories')
            ? BusinessCategory::count()
            : 0;

        $hasVerificationTable = Schema::hasTable('verification_requests');

        $featuredBusinesses = (clone $publishedQuery)
            ->with('category')
            ->latest('published_at')
            ->take(3)
            ->get()
            ->map(function ($b) use ($hasVerificationTable) {
                $isVerified = $hasVerificationTable
                    ? DB::table('verification_requests')
                        ->where('business_id', $b->id)
                        ->where('status', 'approved')
                        ->exists()
                    : false;

                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'description' => $b->description,
                    'district' => $b->district,
                    'category_name' => $b->category?->name ?? 'Umum',
                    'is_verified' => $isVerified,
                    'logo_path' => $b->logo_path,
                    'business_needs' => $b->business_needs,
                ];
            });
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'stats' => [
            'businesses_count' => $publishedCount,
            'categories_count' => $categoriesCount,
        ],
        'featured_businesses' => $featuredBusinesses,
    ]);
})->name('landing');

/*
|--------------------------------------------------------------------------
| Direktori Publik UMKM & Detail Profil (FR-10 / KSA-156, FR-11 / KSA-68)
|--------------------------------------------------------------------------
*/
Route::get('/businesses', [PublicBusinessDirectoryController::class, 'index'])->name('businesses.index');
Route::get('/direktori', [PublicBusinessDirectoryController::class, 'index'])->name('direktori.index');
Route::get('/businesses/{business}', [PublicBusinessProfileController::class, 'show'])->name('public.businesses.show');
Route::get('/direktori/{business}', [PublicBusinessProfileController::class, 'show'])->name('direktori.show');

Route::get('/dashboard', function (Request $request, StockDashboardController $stockDashboard) {
    if ($request->user()->isUmkm()) {
        return $stockDashboard($request);
    }

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

    // Modul Proposal Kolaborasi Bisnis (FR-12 / KSA-88)
    Route::get('/proposals', [CollaborationProposalController::class, 'index'])->name('proposals.index');
    Route::post('/proposals', [CollaborationProposalController::class, 'store'])->name('proposals.store');
    Route::post('/proposals/{proposal}/respond', [CollaborationProposalController::class, 'respond'])->name('proposals.respond');
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
        Route::get('/dashboard', StockDashboardController::class)->name('dashboard');
        Route::get('/inventory', [RestockController::class, 'index'])->name('inventory.index');
        Route::post('/inventory/sales', [StockSaleController::class, 'store'])->name('inventory.sales.store');
        Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
        Route::get('/sales/report', [SalesReportController::class, 'index'])->name('sales.report');
        Route::get('/sales/report/export', [SalesReportController::class, 'export'])->name('sales.report.export');
        Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
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

        // Profil Organisasi Mitra (FR-16 / KSA-72)
        Route::get('/profile', [PartnerProfileController::class, 'edit'])->name('profile.edit');
        Route::post('/profile', [PartnerProfileController::class, 'store'])->name('profile.store');
        Route::get('/profile/{partnerProfile}', [PartnerProfileController::class, 'show'])
            ->can('view', 'partnerProfile')
            ->name('profile.show');
        Route::put('/profile/{partnerProfile}', [PartnerProfileController::class, 'update'])
            ->can('update', 'partnerProfile')
            ->name('profile.update');
        Route::delete('/profile/{partnerProfile}', [PartnerProfileController::class, 'destroy'])
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
