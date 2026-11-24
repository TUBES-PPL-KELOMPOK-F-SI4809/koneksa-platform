<?php

namespace App\Http\Controllers;

use App\Http\Requests\RespondCollaborationProposalRequest;
use App\Http\Requests\StoreCollaborationProposalRequest;
use App\Models\Business;
use App\Models\CollaborationProposal;
use App\Models\CooperationType;
use App\Models\PartnerProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class CollaborationProposalController extends Controller
{
    /**
     * Tampilkan daftar proposal kolaborasi (inbox masuk & keluar) - FR-12.2.
     */
    public function index(Request $request): Response|JsonResponse
    {
        Gate::authorize('viewAny', CollaborationProposal::class);

        $user = $request->user();

        $query = CollaborationProposal::query()
            ->with(['business.category', 'partnerProfile', 'initiator', 'cooperationType'])
            ->latest();

        if ($user->isUmkm()) {
            $businessId = $user->business?->id;
            $incoming = (clone $query)->where('business_id', $businessId)->where('initiated_by', '!=', $user->id)->get();
            $outgoing = (clone $query)->where('business_id', $businessId)->where('initiated_by', $user->id)->get();
        } elseif ($user->isMitra()) {
            $partnerId = $user->partnerProfile?->id;
            $incoming = (clone $query)->where('partner_profile_id', $partnerId)->where('initiated_by', '!=', $user->id)->get();
            $outgoing = (clone $query)->where('partner_profile_id', $partnerId)->where('initiated_by', $user->id)->get();
        } else {
            // Admin atau Pemda melihat seluruh proposal untuk monitoring
            $incoming = $query->get();
            $outgoing = collect();
        }

        $formatProposal = fn (CollaborationProposal $p) => [
            'id' => $p->id,
            'title' => $p->title,
            'description' => $p->description,
            'status' => $p->status,
            'status_label' => match ($p->status) {
                'pending' => 'Menunggu Respon',
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak',
                'ongoing' => 'Sedang Berjalan',
                'completed' => 'Selesai',
                default => ucfirst($p->status),
            },
            'status_tone' => match ($p->status) {
                'pending' => 'warning',
                'accepted', 'ongoing', 'completed' => 'success',
                'rejected' => 'danger',
                default => 'neutral',
            },
            'response_note' => $p->response_note,
            'responded_at' => $p->responded_at?->toIso8601String(),
            'created_at' => $p->created_at?->toIso8601String(),
            'created_at_human' => $p->created_at?->diffForHumans(),
            'cooperation_type' => $p->cooperationType ? [
                'id' => $p->cooperationType->id,
                'name' => $p->cooperationType->name,
            ] : null,
            'business' => $p->business ? [
                'id' => $p->business->id,
                'name' => $p->business->name,
                'district' => $p->business->district,
                'category_name' => $p->business->category?->name,
            ] : null,
            'partner' => $p->partnerProfile ? [
                'id' => $p->partnerProfile->id,
                'organization_name' => $p->partnerProfile->organization_name,
                'industry' => $p->partnerProfile->industry,
            ] : null,
            'initiator' => [
                'id' => $p->initiator?->id,
                'name' => $p->initiator?->name,
                'role' => $p->initiator?->role,
            ],
            'can_respond' => $p->isPending() && $p->isRecipient($user),
        ];

        $payload = [
            'incoming' => $incoming->map($formatProposal),
            'outgoing' => $outgoing->map($formatProposal),
            'cooperation_types' => CooperationType::query()->orderBy('id')->get(['id', 'name']),
            'available_partners' => $user->isUmkm()
                ? PartnerProfile::query()->orderBy('organization_name')->get(['id', 'organization_name', 'industry'])
                : [],
            'available_businesses' => $user->isMitra()
                ? Business::query()->whereNotNull('published_at')->orderBy('name')->get(['id', 'name', 'district'])
                : [],
            'user_role' => $user->role,
        ];

        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return Inertia::render('Proposals/Index', $payload);
    }

    /**
     * Ajukan proposal kolaborasi bisnis baru (FR-12.1).
     */
    public function store(StoreCollaborationProposalRequest $request): RedirectResponse|JsonResponse
    {
        Gate::authorize('create', CollaborationProposal::class);

        $user = $request->user();
        $validated = $request->validated();

        $businessId = $user->isUmkm()
            ? $user->business->id
            : $validated['business_id'];

        $partnerProfileId = $user->isMitra()
            ? $user->partnerProfile->id
            : $validated['partner_profile_id'];

        $proposal = CollaborationProposal::create([
            'business_id' => $businessId,
            'partner_profile_id' => $partnerProfileId,
            'initiated_by' => $user->id,
            'cooperation_type_id' => $validated['cooperation_type_id'],
            'digital_offering_id' => $validated['digital_offering_id'] ?? null,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'attachment_path' => $validated['attachment_path'] ?? null,
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Proposal kolaborasi berhasil diajukan.',
                'proposal' => $proposal->load(['business', 'partnerProfile', 'cooperationType']),
            ], 201);
        }

        return redirect()->back()->with('success', 'Proposal kolaborasi berhasil diajukan.');
    }

    /**
     * Respon (Terima atau Tolak) proposal masuk (FR-12.2 / BR-05).
     */
    public function respond(RespondCollaborationProposalRequest $request, CollaborationProposal $proposal): RedirectResponse|JsonResponse
    {
        Gate::authorize('respond', $proposal);

        $decision = $request->input('decision');
        $newStatus = $decision === 'accept'
            ? CollaborationProposal::STATUS_ACCEPTED
            : CollaborationProposal::STATUS_REJECTED;

        $proposal->update([
            'status' => $newStatus,
            'response_note' => $request->input('response_note'),
            'responded_at' => now(),
            'started_at' => $newStatus === CollaborationProposal::STATUS_ACCEPTED ? now() : null,
        ]);

        $statusText = $newStatus === CollaborationProposal::STATUS_ACCEPTED ? 'diterima' : 'ditolak';
        $message = "Proposal kolaborasi berhasil {$statusText}.";

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $message,
                'proposal' => $proposal->refresh(),
            ]);
        }

        return redirect()->back()->with('success', $message);
    }
}
