<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicBusinessProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedBusiness(?User $owner = null): Business
    {
        $owner = $owner ?? User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);

        return Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => 'Kopi Aroma Parahyangan',
            'description' => 'Produsen kopi arabika lokal khas pegunungan Bandung.',
            'address' => 'Jl. Braga No. 10',
            'district' => 'Sumur Bandung',
            'subdistrict' => 'Braga',
            'contact_phone' => '081234567890',
            'business_needs' => "Distribusi ritel dan pasar modern\nPendanaan mesin roasting kapasitas 10kg",
            'published_at' => now(),
        ]);
    }

    public function test_guest_can_view_published_business_profile_with_all_public_details(): void
    {
        $business = $this->createPublishedBusiness();

        // Buat produk aktif dengan varian
        $product = Product::create([
            'business_id' => $business->id,
            'name' => 'Kopi Arabika 250g',
            'description' => 'Biji kopi sangrai kualitas premium',
            'unit' => 'Pack',
        ]);
        ProductVariant::create([
            'product_id' => $product->id,
            'variant_key' => 'arabika-250g',
            'size' => '250g',
            'selling_price' => 65000,
            'stock_quantity' => 50,
            'low_stock_threshold' => 10,
        ]);

        // Verifikasi administratif disetujui
        DB::table('verification_requests')->insert([
            'business_id' => $business->id,
            'submitted_data' => json_encode(['nib' => '1234567890']),
            'status' => 'approved',
            'reviewed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Transaksi penjualan tercatat
        Sale::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-20261124-001',
            'sold_at' => now(),
            'status' => 'recorded',
            'total_amount' => 130000,
        ]);

        // Pengunjung publik mengakses halaman
        $response = $this->get(route('public.businesses.show', $business));

        $response->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/BusinessDetail')
                ->where('business.id', $business->id)
                ->where('business.name', 'Kopi Aroma Parahyangan')
                ->where('business.category_name', 'Kuliner')
                ->where('business.verification.is_verified', true)
                ->where('business.verification.status', 'approved')
                ->where('business.verification.label', 'Terverifikasi')
                ->where('business.performance.active_products_count', 1)
                ->where('business.performance.total_variants_count', 1)
                ->where('business.performance.completed_sales_count', 1)
                ->has('business.products', 1)
                ->where('business.products.0.name', 'Kopi Arabika 250g')
                ->where('business.products.0.min_price', 65000)
                ->where('business.proposal_action.can_submit_proposal', false)
                ->where('business.proposal_action.requires_auth', true)
            );
    }

    public function test_guest_cannot_view_unpublished_business_profile(): void
    {
        $business = $this->createPublishedBusiness();
        $business->update(['published_at' => null]);

        $this->get(route('public.businesses.show', $business))
            ->assertForbidden();
    }

    public function test_guest_cannot_view_published_business_if_owner_is_disabled(): void
    {
        $owner = User::factory()->umkm()->create(['account_status' => 'disabled']);
        $business = $this->createPublishedBusiness($owner);

        $this->get(route('public.businesses.show', $business))
            ->assertForbidden();
    }

    public function test_guest_cannot_view_published_business_if_owner_is_deleted(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublishedBusiness($owner);
        $owner->delete();

        $this->get(route('public.businesses.show', $business))
            ->assertForbidden();
    }

    public function test_owner_can_view_their_own_business_even_when_unpublished(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublishedBusiness($owner);
        $business->update(['published_at' => null]);

        $this->actingAs($owner)
            ->get(route('public.businesses.show', $business))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/BusinessDetail')
                ->where('business.id', $business->id)
                ->where('business.proposal_action.is_owner', true)
                ->where('business.proposal_action.can_submit_proposal', false)
            );
    }

    public function test_other_umkm_cannot_view_unpublished_business(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublishedBusiness($owner);
        $business->update(['published_at' => null]);

        $otherUmkm = User::factory()->umkm()->create();

        $this->actingAs($otherUmkm)
            ->get(route('public.businesses.show', $business))
            ->assertForbidden();
    }

    public function test_superadmin_and_pemda_can_view_even_when_unpublished(): void
    {
        $business = $this->createPublishedBusiness();
        $business->update(['published_at' => null]);

        $admin = User::factory()->create(['role' => 'admin']);
        $pemda = User::factory()->create(['role' => 'pemda']);

        $this->actingAs($admin)
            ->get(route('public.businesses.show', $business))
            ->assertOk();

        $this->actingAs($pemda)
            ->get(route('public.businesses.show', $business))
            ->assertOk();
    }

    public function test_mitra_industri_has_proposal_submission_enabled_on_published_business(): void
    {
        $business = $this->createPublishedBusiness();
        $mitra = User::factory()->create(['role' => 'mitra', 'account_status' => 'enabled']);

        $this->actingAs($mitra)
            ->get(route('public.businesses.show', $business))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('business.proposal_action.can_submit_proposal', true)
                ->where('business.proposal_action.requires_auth', false)
                ->where('business.proposal_action.is_owner', false)
            );
    }

    public function test_disabled_mitra_cannot_submit_proposal(): void
    {
        $business = $this->createPublishedBusiness();
        $disabledMitra = User::factory()->create(['role' => 'mitra', 'account_status' => 'disabled']);

        $this->actingAs($disabledMitra)
            ->get(route('public.businesses.show', $business))
            ->assertForbidden();
    }

    public function test_verification_status_is_accurately_reflected(): void
    {
        $business = $this->createPublishedBusiness();

        // 1. Belum pernah verifikasi
        $res = $this->getJson(route('public.businesses.show', $business))->assertOk();
        $this->assertFalse($res->json('business.verification.is_verified'));
        $this->assertSame('unverified', $res->json('business.verification.status'));
        $this->assertSame('Belum Terverifikasi', $res->json('business.verification.label'));

        // 2. Pending verifikasi
        $reqId = DB::table('verification_requests')->insertGetId([
            'business_id' => $business->id,
            'submitted_data' => json_encode(['nib' => '1234567890']),
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $res = $this->getJson(route('public.businesses.show', $business))->assertOk();
        $this->assertFalse($res->json('business.verification.is_verified'));
        $this->assertSame('pending', $res->json('business.verification.status'));
        $this->assertSame('Menunggu Verifikasi', $res->json('business.verification.label'));

        // 3. Rejected verifikasi
        DB::table('verification_requests')->where('id', $reqId)->update(['status' => 'rejected']);
        $res = $this->getJson(route('public.businesses.show', $business))->assertOk();
        $this->assertFalse($res->json('business.verification.is_verified'));
        $this->assertSame('rejected', $res->json('business.verification.status'));
        $this->assertSame('Perlu Perbaikan', $res->json('business.verification.label'));

        // 4. Approved verifikasi
        DB::table('verification_requests')->where('id', $reqId)->update(['status' => 'approved', 'reviewed_at' => now()]);
        $res = $this->getJson(route('public.businesses.show', $business))->assertOk();
        $this->assertTrue($res->json('business.verification.is_verified'));
        $this->assertSame('approved', $res->json('business.verification.status'));
        $this->assertSame('Terverifikasi', $res->json('business.verification.label'));
    }

    public function test_only_active_non_archived_products_are_displayed_in_public_catalog(): void
    {
        $business = $this->createPublishedBusiness();

        // Produk 1: Aktif
        $activeProduct = Product::create([
            'business_id' => $business->id,
            'name' => 'Produk Aktif',
            'unit' => 'Pcs',
        ]);
        ProductVariant::create([
            'product_id' => $activeProduct->id,
            'variant_key' => 'active-var',
            'size' => 'Varian Aktif',
            'selling_price' => 20000,
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
        ]);

        // Produk 2: Diarsipkan
        $archivedProduct = Product::create([
            'business_id' => $business->id,
            'name' => 'Produk Diarsipkan',
            'unit' => 'Pcs',
            'archived_at' => now(),
        ]);
        ProductVariant::create([
            'product_id' => $archivedProduct->id,
            'variant_key' => 'archived-var',
            'size' => 'Varian Diarsipkan',
            'selling_price' => 30000,
            'stock_quantity' => 0,
            'low_stock_threshold' => 0,
        ]);

        $res = $this->getJson(route('public.businesses.show', $business))->assertOk();
        $products = $res->json('business.products');

        $this->assertCount(1, $products);
        $this->assertSame('Produk Aktif', $products[0]['name']);
        $this->assertSame(1, $res->json('business.performance.active_products_count'));
    }

    public function test_direktori_alias_route_works_identically(): void
    {
        $business = $this->createPublishedBusiness();

        $this->get(route('direktori.show', $business))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Public/BusinessDetail')
                ->where('business.id', $business->id)
            );
    }
}
