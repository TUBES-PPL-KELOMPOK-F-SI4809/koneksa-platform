<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePemdaUserRequest;
use App\Http\Requests\Admin\UpdateUserStatusRequest;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {}

    /**
     * Menampilkan daftar pengguna dengan filter role, status, dan pencarian (KSA-38 / KSA-39 / FR-03.1).
     */
    public function index(Request $request): Response|JsonResponse
    {
        Gate::authorize('viewAny', User::class);

        $filters = $request->only(['role', 'account_status', 'search']);

        $users = User::query()
            ->when($filters['role'] ?? null, fn ($q, $role) => $q->where('role', $role))
            ->when($filters['account_status'] ?? null, fn ($q, $status) => $q->where('account_status', $status))
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => User::count(),
            'enabled' => User::where('account_status', User::STATUS_ENABLED)->count(),
            'disabled' => User::where('account_status', User::STATUS_DISABLED)->count(),
            'pemda' => User::where('role', User::ROLE_PEMDA)->count(),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'users' => $users->items(),
                'summary' => $summary,
            ]);
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $filters,
            'summary' => $summary,
            'roles' => User::ROLES,
            'statuses' => User::ACCOUNT_STATUSES,
            'status' => session('status'),
        ]);
    }

    /**
     * Membuat akun Pemerintah Daerah (Pemda) baru oleh Superadmin (KSA-38 / KSA-40 / FR-03.2).
     */
    public function storePemda(StorePemdaUserRequest $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validated();

        $pemdaUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => User::ROLE_PEMDA,
            'account_status' => User::STATUS_ENABLED,
            'must_change_password' => true,
            'password' => Hash::make($validated['password']),
        ]);

        $this->auditLogService->log(
            AuditLog::MODULE_USER,
            'user.pemda_created',
            "Superadmin membuat akun Pemerintah Daerah baru: {$pemdaUser->name} ({$pemdaUser->email})",
            $pemdaUser,
            [
                'email' => $pemdaUser->email,
                'role' => User::ROLE_PEMDA,
                'must_change_password' => true,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Akun petugas Pemda berhasil dibuat',
                'user' => $pemdaUser,
            ], 201);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'pemda-user-created');
    }

    /**
     * Mengubah status aktif/nonaktif akun pengguna (KSA-38 / KSA-39 / FR-03.1).
     */
    public function updateStatus(UpdateUserStatusRequest $request, User $user): RedirectResponse|JsonResponse
    {
        $oldStatus = $user->account_status;
        $newStatus = $request->validated('account_status');

        $user->update([
            'account_status' => $newStatus,
        ]);

        $this->auditLogService->log(
            AuditLog::MODULE_SUPERADMIN,
            'user.status_toggled',
            "Superadmin mengubah status akun pengguna {$user->name} dari {$oldStatus} ke {$newStatus}",
            $user,
            [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Status akun pengguna diperbarui',
                'user' => $user,
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'user-status-updated');
    }

    /**
     * Menghapus (soft delete) akun pengguna oleh Superadmin (KSA-38 / FR-03.1).
     */
    public function destroy(Request $request, User $user): RedirectResponse|JsonResponse
    {
        Gate::authorize('delete', $user);

        $this->auditLogService->log(
            AuditLog::MODULE_SUPERADMIN,
            'user.deleted_by_admin',
            "Superadmin menghapus akun pengguna {$user->name} ({$user->email})",
            $user,
            [
                'email' => $user->email,
                'role' => $user->role,
            ]
        );

        $user->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Pengguna berhasil dihapus',
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'user-deleted');
    }
}
