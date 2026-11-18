<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAndOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private BusinessCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = BusinessCategory::create([
            'name' => 'Kuliner & Pangan',
        ]);
    }

    // =========================================================================
    // 1. PENGUJIAN AKSES PENGGUNA ANONIM (UNAUTHENTICATED)
    // =========================================================================

    public function test_guest_is_redirected_to_login_on_private_routes(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/profile')->assertRedirect(route('login'));
        $this->get('/umkm/dashboard')->assertRedirect(route('login'));
        $this->get('/mitra/dashboard')->assertRedirect(route('login'));
        $this->get('/pemda/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_guest_json_request_receives_unauthorized(): void
    {
        $this->getJson('/umkm/dashboard')->assertStatus(401);
        $this->getJson('/admin/users')->assertStatus(401);
    }

    // =========================================================================
    // 2. PENGUJIAN AKUN DINONAKTIFKAN (DISABLED ACCOUNT PROTECTION)
    // =========================================================================

    public function test_disabled_user_access_is_rejected_and_session_invalidated(): void
    {
        $disabledUser = User::factory()->umkm()->disabled()->create();

        $response = $this->actingAs($disabledUser)->get('/umkm/dashboard');

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_disabled_user_json_request_receives_forbidden(): void
    {
        $disabledUser = User::factory()->admin()->disabled()->create();

        $response = $this->actingAs($disabledUser)->getJson('/admin/dashboard');

        $response->assertStatus(403);
    }

    // =========================================================================
    // 3. PENGUJIAN ISOLASI RUTE BERBASIS ROLE (RBAC CROSS-ROLE RESTRICTIONS)
    // =========================================================================

    public function test_umkm_can_access_umkm_routes_but_forbidden_from_others(): void
    {
        $umkm = User::factory()->umkm()->create();

        // Positif
        $this->actingAs($umkm)->get('/umkm/dashboard')->assertStatus(200);

        // Negatif (lintas role)
        $this->actingAs($umkm)->get('/mitra/dashboard')->assertStatus(403);
        $this->actingAs($umkm)->get('/pemda/dashboard')->assertStatus(403);
        $this->actingAs($umkm)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($umkm)->get('/admin/users')->assertStatus(403);
    }

    public function test_mitra_can_access_mitra_routes_but_forbidden_from_others(): void
    {
        $mitra = User::factory()->mitra()->create();

        // Positif
        $this->actingAs($mitra)->get('/mitra/dashboard')->assertStatus(200);

        // Negatif
        $this->actingAs($mitra)->get('/umkm/dashboard')->assertStatus(403);
        $this->actingAs($mitra)->get('/pemda/dashboard')->assertStatus(403);
        $this->actingAs($mitra)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_pemda_can_access_pemda_routes_but_forbidden_from_others(): void
    {
        $pemda = User::factory()->pemda()->create();

        // Positif
        $this->actingAs($pemda)->get('/pemda/dashboard')->assertStatus(200);
        $this->actingAs($pemda)->get('/pemda/monitoring')->assertStatus(200);

        // Negatif
        $this->actingAs($pemda)->get('/umkm/dashboard')->assertStatus(403);
        $this->actingAs($pemda)->get('/mitra/dashboard')->assertStatus(403);
        $this->actingAs($pemda)->get('/admin/dashboard')->assertStatus(403);
        $this->actingAs($pemda)->get('/admin/users')->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes_but_forbidden_from_user_dashboards(): void
    {
        $admin = User::factory()->admin()->create();

        // Positif
        $this->actingAs($admin)->get('/admin/dashboard')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/users')->assertStatus(200);

        // Negatif
        $this->actingAs($admin)->get('/umkm/dashboard')->assertStatus(403);
        $this->actingAs($admin)->get('/mitra/dashboard')->assertStatus(403);
        $this->actingAs($admin)->get('/pemda/dashboard')->assertStatus(403);
    }

    // =========================================================================
    // 4. PENGUJIAN RESOURCE OWNERSHIP — PROFIL USAHA UMKM (BUSINESS)
    // =========================================================================

    public function test_umkm_can_update_own_business(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $this->category->id,
            'name' => 'Warung Kopi Milik Sendiri',
        ]);

        $response = $this->actingAs($owner)->putJson("/umkm/business/{$business->id}", [
            'name' => 'Nama Warung Diperbarui',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Profil usaha berhasil diperbarui']);
    }

    public function test_umkm_cannot_update_or_delete_other_umkm_business(): void
    {
        $owner = User::factory()->umkm()->create();
        $stranger = User::factory()->umkm()->create();

        $business = Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $this->category->id,
            'name' => 'Usaha Korban',
        ]);

        // Percobaan modifikasi resource milik user lain ditolak 403
        $this->actingAs($stranger)
            ->putJson("/umkm/business/{$business->id}", ['name' => 'Pembajakan Usaha'])
            ->assertStatus(403);

        // Percobaan penghapusan resource milik user lain ditolak 403
        $this->actingAs($stranger)
            ->deleteJson("/umkm/business/{$business->id}")
            ->assertStatus(403);
    }

    public function test_published_vs_unpublished_business_visibility(): void
    {
        $owner = User::factory()->umkm()->create();
        $otherUmkm = User::factory()->umkm()->create();
        $pemda = User::factory()->pemda()->create();

        // 1. Usaha Draft (Unpublished)
        $draftBusiness = Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $this->category->id,
            'name' => 'Usaha Masih Draf',
            'published_at' => null,
        ]);

        // Pemilik dapat melihat usahanya
        $this->actingAs($owner)->getJson("/umkm/business/{$draftBusiness->id}")->assertStatus(200);

        // UMKM lain tidak dapat melihat draft
        $this->actingAs($otherUmkm)->getJson("/umkm/business/{$draftBusiness->id}")->assertStatus(403);

        // Pemda dapat melihat untuk kepentingan monitoring & verifikasi (BR-13)
        $this->actingAs($pemda)->getJson("/pemda/umkm/{$draftBusiness->id}")->assertStatus(200);

        // 2. Usaha Dipublikasikan (Published)
        $owner2 = User::factory()->umkm()->create();
        $publishedBusiness = Business::create([
            'user_id' => $owner2->id,
            'business_category_id' => $this->category->id,
            'name' => 'Usaha Publik',
            'published_at' => now(),
        ]);

        // UMKM lain dapat melihat profil publik
        $this->actingAs($otherUmkm)->getJson("/umkm/business/{$publishedBusiness->id}")->assertStatus(200);
        $this->actingAs($pemda)->getJson("/pemda/umkm/{$publishedBusiness->id}")->assertStatus(200);
    }

    // =========================================================================
    // 5. PENGUJIAN RESOURCE OWNERSHIP — PROFIL MITRA INDUSTRI (PARTNER PROFILE)
    // =========================================================================

    public function test_mitra_can_update_own_partner_profile(): void
    {
        $owner = User::factory()->mitra()->create();
        $profile = PartnerProfile::create([
            'user_id' => $owner->id,
            'organization_name' => 'PT Mitra Sejahtera',
        ]);

        $response = $this->actingAs($owner)->putJson("/mitra/profile/{$profile->id}", [
            'organization_name' => 'PT Mitra Sejahtera Makmur',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Profil organisasi mitra berhasil diperbarui']);
    }

    public function test_mitra_cannot_update_or_delete_other_mitra_profile(): void
    {
        $owner = User::factory()->mitra()->create();
        $stranger = User::factory()->mitra()->create();

        $profile = PartnerProfile::create([
            'user_id' => $owner->id,
            'organization_name' => 'PT Korban Industri',
        ]);

        // Ditolak 403
        $this->actingAs($stranger)
            ->putJson("/mitra/profile/{$profile->id}", ['organization_name' => 'PT Dibajak'])
            ->assertStatus(403);

        $this->actingAs($stranger)
            ->deleteJson("/mitra/profile/{$profile->id}")
            ->assertStatus(403);
    }

    // =========================================================================
    // 6. PENGUJIAN ADMINISTRASI SUPERADMIN & SELF-PROTECTION INVARIANT
    // =========================================================================

    public function test_admin_can_update_other_user_status(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->umkm()->create(['account_status' => User::STATUS_ENABLED]);

        $response = $this->actingAs($admin)->patchJson("/admin/users/{$targetUser->id}/status", [
            'account_status' => User::STATUS_DISABLED,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(User::STATUS_DISABLED, $targetUser->fresh()->account_status);
    }

    public function test_admin_cannot_disable_or_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create();

        // Superadmin tidak boleh menonaktifkan dirinya sendiri
        $responseDisable = $this->actingAs($admin)->patchJson("/admin/users/{$admin->id}/status", [
            'account_status' => User::STATUS_DISABLED,
        ]);
        $responseDisable->assertStatus(403);

        // Superadmin tidak boleh menghapus dirinya sendiri melalui rute admin
        $responseDelete = $this->actingAs($admin)->deleteJson("/admin/users/{$admin->id}");
        $responseDelete->assertStatus(403);
    }

    public function test_non_admin_cannot_mutate_user_status(): void
    {
        $umkm = User::factory()->umkm()->create();
        $targetUser = User::factory()->mitra()->create();

        $this->actingAs($umkm)
            ->patchJson("/admin/users/{$targetUser->id}/status", ['account_status' => User::STATUS_DISABLED])
            ->assertStatus(403);
    }
}
