<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\CollaborationProposal;
use App\Models\CooperationType;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class CollaborationProposalTest extends TestCase
{
    use RefreshDatabase;

    private function createCooperationType(string $name = 'Distribusi & Reseller', string $code = 'distribution'): CooperationType
    {
        return CooperationType::create([
            'name' => $name,
            'code' => $code,
            'description' => 'Kerja sama distribusi pasokan barang retail.',
            'is_active' => true,
        ]);
    }

    private function createUmkmWithBusiness(?string $businessName = 'Kopi Aroma Bandung'): array
    {
        $user = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);

        $business = Business::create([
            'user_id' => $user->id,
            'business_category_id' => $category->id,
            'name' => $businessName,
            'description' => 'Produsen kopi terkemuka di Kota Bandung.',
            'address' => 'Jl. Braga No. 10',
            'district' => 'Sumur Bandung',
            'subdistrict' => 'Braga',
            'contact_phone' => '081234567890',
            'business_needs' => 'Perluasan jaringan retail ritel modern',
            'published_at' => now(),
        ]);

        return [$user, $business];
    }

    private function createMitraWithProfile(?string $orgName = 'PT Retail Nusantara Sejahtera'): array
    {
        $user = User::factory()->mitra()->create();

        $profile = PartnerProfile::create([
            'user_id' => $user->id,
            'organization_name' => $orgName,
            'industry' => 'Ritel Modern & Distribusi FMCG',
            'description' => 'Jaringan distribusi supermarket dan minimarket terkemuka.',
            'address' => 'Jl. Asia Afrika No. 45',
            'contact_phone' => '081298765432',
            'website_url' => 'https://retailnusantara.example.com',
        ]);

        return [$user, $profile];
    }

    public function test_guest_cannot_access_proposals_page_and_is_redirected_to_login(): void
    {
        $response = $this->get(route('proposals.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_submit_proposal(): void
    {
        $coopType = $this->createCooperationType();
        [, $business] = $this->createUmkmWithBusiness();

        $response = $this->post(route('proposals.store'), [
            'business_id' => $business->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Penawaran Pasokan Barang',
            'description' => 'Penawaran suplai kopi ritel bulanan.',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_mitra_can_submit_collaboration_proposal_to_umkm_business_fr_12_1(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $response = $this->actingAs($mitraUser)->post(route('proposals.store'), [
            'business_id' => $business->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Kerja Sama Distribusi Kopi ke 20 Gerai Supermarket',
            'description' => 'Kami menawarkan penempatan produk kopi di rak premium seluruh gerai ritel kami dengan skema konsinyasi 30 hari.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('collaboration_proposals', [
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Kerja Sama Distribusi Kopi ke 20 Gerai Supermarket',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);
    }

    public function test_umkm_can_submit_collaboration_proposal_to_mitra_partner_fr_12_1(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $response = $this->actingAs($umkmUser)->post(route('proposals.store'), [
            'partner_profile_id' => $mitraProfile->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Permohonan Distribusi Produk Lokal Bandung',
            'description' => 'Kami mengajukan proposal pasokan produk kopi kemasan premium untuk mengisi etalase produk lokal Jawa Barat.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('collaboration_proposals', [
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $umkmUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Permohonan Distribusi Produk Lokal Bandung',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);
    }

    public function test_submitting_proposal_requires_mandatory_fields(): void
    {
        [$mitraUser] = $this->createMitraWithProfile();

        $response = $this->actingAs($mitraUser)->post(route('proposals.store'), [
            'business_id' => null,
            'cooperation_type_id' => null,
            'title' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors([
            'business_id',
            'cooperation_type_id',
            'title',
            'description',
        ]);
    }

    public function test_mitra_cannot_submit_proposal_to_non_existent_business(): void
    {
        $coopType = $this->createCooperationType();
        [$mitraUser] = $this->createMitraWithProfile();

        $response = $this->actingAs($mitraUser)->post(route('proposals.store'), [
            'business_id' => 99999,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Kolaborasi Bisnis',
            'description' => 'Rincian deskripsi penawaran kerja sama bisnis.',
        ]);

        $response->assertSessionHasErrors(['business_id']);
    }

    public function test_user_cannot_submit_proposal_to_their_own_business(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();

        // Misal user mencoba mengirim proposal dengan target usaha miliknya sendiri
        $response = $this->actingAs($umkmUser)->post(route('proposals.store'), [
            'partner_profile_id' => 99999,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Kolaborasi Bisnis',
            'description' => 'Rincian deskripsi penawaran kerja sama bisnis.',
        ]);

        $response->assertSessionHasErrors(['partner_profile_id']);
    }

    public function test_authenticated_umkm_can_view_proposals_index_with_incoming_proposals_fr_12_2(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        // Proposal masuk dari Mitra ke UMKM
        CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Penawaran Konsinyasi Produk Retail',
            'description' => 'Distribusi 500 pack kopi bubuk per bulan.',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        $response = $this->actingAs($umkmUser)->get(route('proposals.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Proposals/Index')
            ->has('incoming', 1)
            ->where('incoming.0.title', 'Penawaran Konsinyasi Produk Retail')
            ->where('incoming.0.can_respond', true)
            ->has('outgoing', 0)
        );
    }

    public function test_recipient_umkm_can_accept_pending_proposal_fr_12_2(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $proposal = CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Penawaran Distribusi Ritel',
            'description' => 'Rincian kerja sama distribusi produk.',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        $response = $this->actingAs($umkmUser)->post(route('proposals.respond', $proposal), [
            'decision' => 'accept',
            'response_note' => 'Kami menyetujui tawaran kerja sama ini. Tim kami akan menyiapkan pasokan pertama.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $proposal->refresh();
        $this->assertEquals(CollaborationProposal::STATUS_ACCEPTED, $proposal->status);
        $this->assertEquals('Kami menyetujui tawaran kerja sama ini. Tim kami akan menyiapkan pasokan pertama.', $proposal->response_note);
        $this->assertNotNull($proposal->responded_at);
        $this->assertNotNull($proposal->started_at);
    }

    public function test_recipient_umkm_can_reject_pending_proposal_with_response_note_fr_12_2(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $proposal = CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Penawaran Maklon Kopi Brand Sendiri',
            'description' => 'Maklon 10.000 kaleng kopi per kuartal.',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        $response = $this->actingAs($umkmUser)->post(route('proposals.respond', $proposal), [
            'decision' => 'reject',
            'response_note' => 'Mohon maaf, saat ini kapasitas produksi pabrik kami sedang penuh untuk kuartal ini.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $proposal->refresh();
        $this->assertEquals(CollaborationProposal::STATUS_REJECTED, $proposal->status);
        $this->assertEquals('Mohon maaf, saat ini kapasitas produksi pabrik kami sedang penuh untuk kuartal ini.', $proposal->response_note);
        $this->assertNotNull($proposal->responded_at);
        $this->assertNull($proposal->started_at);
    }

    public function test_initiator_mitra_cannot_accept_or_reject_their_own_proposal_br_05(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $proposal = CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Distribusi Retail',
            'description' => 'Rincian kerja sama distribusi produk.',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        // Inisiator (Mitra) mencoba mengeksekusi respond pada proposal buatannya sendiri
        $response = $this->actingAs($mitraUser)->post(route('proposals.respond', $proposal), [
            'decision' => 'accept',
            'response_note' => 'Menerima sendiri proposal ini',
        ]);

        $response->assertForbidden();

        $proposal->refresh();
        $this->assertEquals(CollaborationProposal::STATUS_PENDING, $proposal->status);
    }

    public function test_unrelated_third_party_user_cannot_respond_to_proposal(): void
    {
        $coopType = $this->createCooperationType();
        [, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();
        [$thirdPartyUser] = $this->createUmkmWithBusiness('UMKM Lain');

        $proposal = CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Distribusi Retail',
            'description' => 'Rincian kerja sama distribusi produk.',
            'status' => CollaborationProposal::STATUS_PENDING,
        ]);

        // User UMKM ketiga yang tidak terlibat dalam proposal
        $response = $this->actingAs($thirdPartyUser)->post(route('proposals.respond', $proposal), [
            'decision' => 'accept',
            'response_note' => 'Menyetujui',
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_respond_to_already_decided_proposal(): void
    {
        $coopType = $this->createCooperationType();
        [$umkmUser, $business] = $this->createUmkmWithBusiness();
        [$mitraUser, $mitraProfile] = $this->createMitraWithProfile();

        $proposal = CollaborationProposal::create([
            'business_id' => $business->id,
            'partner_profile_id' => $mitraProfile->id,
            'initiated_by' => $mitraUser->id,
            'cooperation_type_id' => $coopType->id,
            'title' => 'Proposal Distribusi Retail',
            'description' => 'Rincian kerja sama distribusi produk.',
            'status' => CollaborationProposal::STATUS_ACCEPTED, // Sudah diterima sebelumnya
            'responded_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($umkmUser)->post(route('proposals.respond', $proposal), [
            'decision' => 'reject',
            'response_note' => 'Mengubah keputusan',
        ]);

        $response->assertForbidden();
    }
}
