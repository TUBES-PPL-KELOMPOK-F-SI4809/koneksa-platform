<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AuditLogController extends Controller
{
    /**
     * Menampilkan daftar riwayat audit trail (KSA-152 / KSA-155 / FR-32.2).
     * Hanya dapat diakses oleh peran Superadmin (admin).
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['module', 'action', 'actor_role', 'search', 'date_from', 'date_to']);

        $logs = AuditLog::with('actor:id,name,email,role')
            ->filter($filters)
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('AuditLog/Index', [
            'logs' => $logs,
            'filters' => $filters,
            'modules' => AuditLog::MODULES,
        ]);
    }
}
