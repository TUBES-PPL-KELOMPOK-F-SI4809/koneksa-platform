<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupervisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_access_supervision_panel(): void
    {
        // Guest -> redirect ke login
        $this->get(route('admin.supervision.index'))->assertRedirect(route('login'));

        // UMKM, Mitra, Pemda -> 403 Forbidden
        $umkm = User::factory()->umkm()->create();
        $mitra = User::factory()->mitra()->create();
        $pemda = User::factory()->pemda()->create();

        $this->actingAs($umkm)->get(route('admin.supervision.index'))->assertForbidden();
        $this->actingAs($mitra)->get(route('admin.supervision.index'))->assertForbidden();
        $this->actingAs($pemda)->get(route('admin.supervision.index'))->assertForbidden();

        // Superadmin -> 200 OK
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.supervision.index'))->assertOk();
    }

    public function test_supervision_panel_displays_cross_module_metrics_and_entities(): void
    {
        $admin = User::factory()->admin()->create();
        $umkmUser = User::factory()->umkm()->create();
        $mitraUser = User::factory()->mitra()->create();

        $category = BusinessCategory::create(['name' => 'Kriya & Kerajinan']);

        Business::create([
            'user_id' => $umkmUser->id,
            'business_category_id' => $category->id,
            'name' => 'Batik Nusantara Jaya',
            'district' => 'Kota Bandung',
            'published_at' => now(),
        ]);

        PartnerProfile::create([
            'user_id' => $mitraUser->id,
            'organization_name' => 'PT Mitra Teknologi Indonesia',
            'contact_phone' => '081299998888',
        ]);

        $response = $this->actingAs($admin)->getJson(route('admin.supervision.index'));

        $response->assertOk();
        $this->assertSame(1, $response->json('metrics.businesses_total'));
        $this->assertSame(1, $response->json('metrics.businesses_published'));
        $this->assertSame(1, $response->json('metrics.partners_total'));
        $this->assertSame(1, $response->json('metrics.categories_total'));
    }

    public function test_superadmin_can_publish_and_suspend_business_profile_with_actor_id_recorded(): void
    {
        $admin = User::factory()->admin()->create();
        $umkmUser = User::factory()->umkm()->create();
        $category = BusinessCategory::create(['name' => 'Kuliner']);

        $business = Business::create([
            'user_id' => $umkmUser->id,
            'business_category_id' => $category->id,
            'name' => 'Kopi Senja Bandung',
            'published_at' => null,
        ]);

        // Publikasikan profil usaha UMKM melalui supervisi Superadmin
        $this->actingAs($admin)
            ->patch(route('admin.businesses.publication', $business), [
                'publish' => true,
                'reason' => 'Memenuhi standar kelengkapan direktori',
            ])
            ->assertRedirect(route('admin.supervision.index'));

        $this->assertNotNull($business->refresh()->published_at);

        // Pastikan Actor ID Superadmin asli tercatat di Audit Trail (BR-15 / FR-31.2)
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'actor_role' => User::ROLE_ADMIN,
            'module' => AuditLog::MODULE_SUPERADMIN,
            'action' => 'superadmin.cross_role_action',
            'auditable_id' => $business->id,
        ]);

        // Tangguhkan kembali publikasi
        $this->actingAs($admin)
            ->patch(route('admin.businesses.publication', $business), [
                'publish' => false,
                'reason' => 'Peninjauan ulang informasi kontak',
            ])
            ->assertRedirect(route('admin.supervision.index'));

        $this->assertNull($business->refresh()->published_at);
    }

    public function test_non_admin_cannot_execute_superadmin_business_publication_override(): void
    {
        $umkmUser = User::factory()->umkm()->create();
        $pemdaUser = User::factory()->pemda()->create();
        $category = BusinessCategory::create(['name' => 'Jasa']);

        $business = Business::create([
            'user_id' => $umkmUser->id,
            'business_category_id' => $category->id,
            'name' => 'Usaha Mandiri',
            'published_at' => null,
        ]);

        $this->actingAs($umkmUser)
            ->patch(route('admin.businesses.publication', $business), ['publish' => true])
            ->assertForbidden();

        $this->actingAs($pemdaUser)
            ->patch(route('admin.businesses.publication', $business), ['publish' => true])
            ->assertForbidden();

        $this->assertNull($business->refresh()->published_at);
    }

    public function test_superadmin_can_create_master_business_category_with_audit_log(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Agribisnis & Pertanian',
            ]);

        $response->assertRedirect(route('admin.supervision.index'));
        $response->assertSessionHas('status', 'category-created');

        $category = BusinessCategory::where('name', 'Agribisnis & Pertanian')->first();
        $this->assertNotNull($category);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'actor_role' => User::ROLE_ADMIN,
            'module' => AuditLog::MODULE_SUPERADMIN,
            'action' => 'superadmin.category_created',
            'auditable_id' => $category->id,
        ]);
    }

    public function test_non_admin_cannot_create_master_business_category(): void
    {
        $mitra = User::factory()->mitra()->create();

        $this->actingAs($mitra)
            ->post(route('admin.categories.store'), ['name' => 'Kategori Ilegal'])
            ->assertForbidden();

        $this->assertDatabaseMissing('business_categories', ['name' => 'Kategori Ilegal']);
    }

    public function test_duplicate_master_business_category_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        BusinessCategory::create(['name' => 'Fashion & Tekstil']);

        $response = $this->actingAs($admin)
            ->from(route('admin.supervision.index'))
            ->post(route('admin.categories.store'), [
                'name' => 'Fashion & Tekstil',
            ]);

        $response->assertSessionHasErrors('name');
    }
}
