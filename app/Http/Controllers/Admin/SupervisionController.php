<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\PartnerProfile;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class SupervisionController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Menampilkan Panel Supervisi Entitas Sistem Lintas Modul (KSA-148 / KSA-150 / FR-31.1).
     * Hanya dapat diakses oleh Superadmin tanpa menduplikasi halaman Manajemen Pengguna (KSA-38).
     */
    public function index(Request $request): Response|JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $businesses = Business::with(['user:id,name,email', 'category:id,name'])
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $partners = PartnerProfile::with('user:id,name,email')
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $categories = BusinessCategory::withCount('businesses')
            ->orderBy('name')
            ->get();

        $metrics = [
            'businesses_total' => Business::count(),
            'businesses_published' => Business::whereNotNull('published_at')->count(),
            'partners_total' => PartnerProfile::count(),
            'categories_total' => BusinessCategory::count(),
            'verifications_total' => DB::table('verification_requests')->count(),
            'programs_total' => DB::table('programs')->count(),
            'collaborations_total' => DB::table('collaboration_proposals')->count(),
            'audit_logs_total' => AuditLog::count(),
        ];

        $recentAudits = AuditLog::with('actor:id,name,role')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        if ($request->wantsJson()) {
            return response()->json([
                'metrics' => $metrics,
                'businesses' => $businesses,
                'partners' => $partners,
                'categories' => $categories,
                'recent_audits' => $recentAudits,
            ]);
        }

        return Inertia::render('Admin/Supervision/Index', [
            'metrics' => $metrics,
            'businesses' => $businesses,
            'partners' => $partners,
            'categories' => $categories,
            'recentAudits' => $recentAudits,
            'status' => session('status'),
        ]);
    }

    /**
     * Operasi Administratif Lintas Modul: Mengubah status publikasi profil usaha UMKM
     * dengan pencatatan Actor ID Superadmin pada Audit Trail (KSA-149 / FR-31.2 / BR-15).
     */
    public function toggleBusinessPublication(Request $request, Business $business): RedirectResponse|JsonResponse
    {
        Gate::authorize('supervise', $business);

        $validated = $request->validate([
            'publish' => ['required', 'boolean'],
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        $shouldPublish = (bool) $validated['publish'];
        $reason = $validated['reason'] ?? 'Supervisi administratif Superadmin';

        $business->update([
            'published_at' => $shouldPublish ? now() : null,
        ]);

        $actionLabel = $shouldPublish ? 'dipublikasikan' : 'ditangguhkan publikasinya';

        $this->auditLogService->log(
            AuditLog::MODULE_SUPERADMIN,
            'superadmin.cross_role_action',
            "Superadmin mengubah status usaha UMKM {$business->name} menjadi {$actionLabel}",
            $business,
            [
                'target_entity' => 'business',
                'published' => $shouldPublish,
                'reason' => $reason,
                'owner_user_id' => $business->user_id,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => "Status publikasi usaha berhasil {$actionLabel}",
                'business' => $business->fresh(),
            ]);
        }

        return redirect()
            ->route('admin.supervision.index')
            ->with('status', 'business-publication-updated');
    }

    /**
     * Operasi Master Data: Menambahkan kategori usaha referensi oleh Superadmin (KSA-149).
     */
    public function storeCategory(Request $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:business_categories,name'],
        ]);

        $category = BusinessCategory::create([
            'name' => $validated['name'],
        ]);

        $this->auditLogService->log(
            AuditLog::MODULE_SUPERADMIN,
            'superadmin.category_created',
            "Superadmin menambahkan kategori usaha master: {$category->name}",
            $category,
            [
                'category_name' => $category->name,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Kategori usaha berhasil ditambahkan',
                'category' => $category,
            ], 201);
        }

        return redirect()
            ->route('admin.supervision.index')
            ->with('status', 'category-created');
    }
}
