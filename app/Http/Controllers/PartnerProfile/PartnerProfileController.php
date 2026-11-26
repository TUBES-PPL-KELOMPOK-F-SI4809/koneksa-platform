<?php

namespace App\Http\Controllers\PartnerProfile;

use App\Http\Controllers\Controller;
use App\Http\Requests\PartnerProfile\StorePartnerProfileRequest;
use App\Http\Requests\PartnerProfile\UpdatePartnerProfileRequest;
use App\Models\CooperationType;
use App\Models\PartnerProfile;
use App\Services\PartnerProfileService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class PartnerProfileController extends Controller
{
    /**
     * Tampilkan halaman form profil organisasi mitra dan preferensi dukungan bisnis (FR-16.1, FR-16.2).
     */
    public function edit(Request $request): Response
    {
        Gate::authorize('manage', PartnerProfile::class);

        $partnerProfile = $request->user()
            ->partnerProfile()
            ->with('cooperationTypes:id,name')
            ->first();

        return Inertia::render('PartnerProfile/Edit', [
            'partnerProfile' => $partnerProfile ? [
                ...$partnerProfile->toArray(),
                'cooperation_type_ids' => $partnerProfile->cooperationTypes
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
                    ->values()
                    ->all(),
            ] : null,
            'cooperationTypes' => CooperationType::query()
                ->orderBy('id')
                ->get(['id', 'name']),
            'entityCategories' => PartnerProfile::ENTITY_CATEGORIES,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * Simpan profil organisasi mitra baru (1 akun Mitra = 1 profil organisasi).
     */
    public function store(StorePartnerProfileRequest $request, PartnerProfileService $service): RedirectResponse|JsonResponse
    {
        Gate::authorize('manage', PartnerProfile::class);

        $profile = $service->create($request->user(), $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profil organisasi mitra berhasil dibuat',
                'partner_profile' => $profile,
            ], 201);
        }

        return redirect()
            ->route('mitra.profile.edit')
            ->with('status', 'Profil organisasi mitra berhasil dibuat.');
    }

    /**
     * Tampilkan detail profil organisasi mitra beserta jenis kerja sama.
     */
    public function show(PartnerProfile $partnerProfile): JsonResponse
    {
        $partnerProfile->load('cooperationTypes:id,name');

        return response()->json($partnerProfile);
    }

    /**
     * Perbarui profil organisasi mitra dan dukungan bisnis milik pemilik.
     */
    public function update(
        UpdatePartnerProfileRequest $request,
        PartnerProfile $partnerProfile,
        PartnerProfileService $service
    ): RedirectResponse|JsonResponse {
        $updated = $service->update($partnerProfile, $request->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Profil organisasi mitra berhasil diperbarui',
                'partner_profile' => $updated,
            ]);
        }

        return redirect()
            ->route('mitra.profile.edit')
            ->with('status', 'Profil organisasi mitra berhasil diperbarui.');
    }

    /**
     * Hapus profil organisasi mitra milik pemilik apabila tidak terkunci oleh relasi aktif.
     */
    public function destroy(Request $request, PartnerProfile $partnerProfile): RedirectResponse|JsonResponse
    {
        try {
            $partnerProfile->delete();
        } catch (QueryException $exception) {
            report($exception);

            $message = 'Profil organisasi tidak dapat dihapus karena masih terkait dengan proposal atau penawaran digital.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 409);
            }

            return back()->withErrors(['partner_profile' => $message]);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Profil organisasi mitra berhasil dihapus']);
        }

        return redirect()
            ->route('mitra.profile.edit')
            ->with('status', 'Profil organisasi mitra berhasil dihapus.');
    }
}