<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use App\Models\VerificationRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicBusinessDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedBusiness(
        string $name = 'Kopi Aroma Bandung',
        ?BusinessCategory $category = null,
        ?User $owner = null,
        ?string $description = null
    ): Business {
        $owner = $owner ?? User::factory()->umkm()->create();
        $category = $category ?? BusinessCategory::firstOrCreate(['name' => 'Kuliner']);

        return Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => $name,
            'description' => $description ?? "Deskripsi operasional usaha {$name}.",
            'address' => 'Jl. Braga No. 10',
            'district' => 'Sumur Bandung',
            'subdistrict' => 'Braga',
            'contact_phone' => '081234567890',
            'business_needs' => 'Perluasan distribusi ritel modern',
            'published_at' => now(),
        ]);
    }

    public function test_guest_can_view_published_businesses_in_directory_fr_10_1(): void
    {
        $business = $this->createPublishedBusiness('Kopi Aroma Bandung');

        $response = $this->get(route('businesses.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/BusinessDirectory')
            ->has('businesses.data', 1)
            ->where('businesses.data.0.id', $business->id)
            ->where('businesses.data.0.name', 'Kopi Aroma Bandung')
            ->has('categories')
            ->where('total_count', 1)
        );
    }

    public function test_unpublished_businesses_are_excluded_from_directory_fr_10_1(): void
    {
        $published = $this->createPublishedBusiness('UMKM Terpublikasi');

        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => 'UMKM Draft / Privat',
            'description' => 'Profil belum dipublikasikan.',
            'published_at' => null, // Draft
        ]);

        $response = $this->getJson(route('businesses.index'));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(1, $data);
        $this->assertEquals($published->id, $data[0]['id']);
        $this->assertEquals('UMKM Terpublikasi', $data[0]['name']);
    }

    public function test_businesses_with_disabled_user_are_excluded_from_directory(): void
    {
        $activeBusiness = $this->createPublishedBusiness('UMKM Aktif');

        $disabledUser = User::factory()->umkm()->disabled()->create();
        $this->createPublishedBusiness('UMKM User Dinonaktifkan', null, $disabledUser);

        $response = $this->getJson(route('businesses.index'));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(1, $data);
        $this->assertEquals($activeBusiness->id, $data[0]['id']);
    }

    public function test_can_filter_businesses_by_name_search_fr_10_2(): void
    {
        $businessKopi = $this->createPublishedBusiness('Kopi Aroma Braga');
        $businessSepatu = $this->createPublishedBusiness('Sepatu Kulit Cibaduyut');

        // Pencarian "Kopi"
        $response = $this->getJson(route('businesses.index', ['search' => 'Kopi']));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(1, $data);
        $this->assertEquals($businessKopi->id, $data[0]['id']);
        $this->assertEquals('Kopi Aroma Braga', $data[0]['name']);

        // Pencarian "Cibaduyut"
        $responseSepatu = $this->getJson(route('businesses.index', ['search' => 'Cibaduyut']));
        $responseSepatu->assertOk();
        $dataSepatu = $responseSepatu->json('businesses.data');

        $this->assertCount(1, $dataSepatu);
        $this->assertEquals($businessSepatu->id, $dataSepatu[0]['id']);
    }

    public function test_can_filter_businesses_by_category_fr_10_2(): void
    {
        $catKuliner = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $catFashion = BusinessCategory::firstOrCreate(['name' => 'Fashion']);

        $businessKuliner = $this->createPublishedBusiness('Kue Balok Kang Isur', $catKuliner);
        $businessFashion = $this->createPublishedBusiness('Kaos Distro Bandung', $catFashion);

        $response = $this->getJson(route('businesses.index', ['category_id' => $catKuliner->id]));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(1, $data);
        $this->assertEquals($businessKuliner->id, $data[0]['id']);
        $this->assertEquals('Kuliner', $data[0]['category_name']);
    }

    public function test_can_combine_name_search_and_category_filter_fr_10_2(): void
    {
        $catKuliner = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $catFashion = BusinessCategory::firstOrCreate(['name' => 'Fashion']);

        $b1 = $this->createPublishedBusiness('Kopi Sanger Aceh', $catKuliner);
        $b2 = $this->createPublishedBusiness('Kopi Shirt Merch', $catFashion);
        $b3 = $this->createPublishedBusiness('Roti Bakar Dago', $catKuliner);

        $response = $this->getJson(route('businesses.index', [
            'search' => 'Kopi',
            'category_id' => $catKuliner->id,
        ]));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(1, $data);
        $this->assertEquals($b1->id, $data[0]['id']);
    }

    public function test_directory_returns_empty_when_no_business_matches_filter(): void
    {
        $this->createPublishedBusiness('Kopi Aroma');

        $response = $this->getJson(route('businesses.index', ['search' => 'KataKunciTidakAda']));

        $response->assertOk();
        $data = $response->json('businesses.data');

        $this->assertCount(0, $data);
        $this->assertEquals(0, $response->json('total_count'));
    }

    public function test_directory_supports_json_response_with_pagination(): void
    {
        for ($i = 1; $i <= 15; $i++) {
            $this->createPublishedBusiness("UMKM {$i}");
        }

        $response = $this->getJson(route('businesses.index', ['page' => 1]));

        $response->assertOk();
        $response->assertJsonStructure([
            'businesses' => [
                'data',
                'current_page',
                'last_page',
                'total',
            ],
            'categories',
            'filters' => ['search', 'category_id'],
            'total_count',
        ]);

        $this->assertCount(12, $response->json('businesses.data'));
        $this->assertEquals(15, $response->json('total_count'));
    }

    public function test_mitra_industri_can_access_directory(): void
    {
        $mitraUser = User::factory()->mitra()->create();
        $this->createPublishedBusiness('Kopi Aroma');

        $response = $this->actingAs($mitraUser)->get(route('businesses.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/BusinessDirectory')
            ->has('businesses.data', 1)
        );
    }

    public function test_direktori_alias_route_works_identically(): void
    {
        $business = $this->createPublishedBusiness('Kopi Aroma');

        $response = $this->get(route('direktori.index'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Public/BusinessDirectory')
            ->has('businesses.data', 1)
            ->where('businesses.data.0.id', $business->id)
        );
    }

    public function test_verification_badge_is_accurately_reflected_in_directory(): void
    {
        $verifiedBusiness = $this->createPublishedBusiness('UMKM Terverifikasi');
        $unverifiedBusiness = $this->createPublishedBusiness('UMKM Belum Terverifikasi');

        VerificationRequest::create([
            'business_id' => $verifiedBusiness->id,
            'submitted_data' => ['nib' => '1234567890'],
            'status' => 'approved',
            'reviewed_at' => now(),
        ]);

        $response = $this->getJson(route('businesses.index'));

        $response->assertOk();
        $data = collect($response->json('businesses.data'));

        $vItem = $data->firstWhere('id', $verifiedBusiness->id);
        $uvItem = $data->firstWhere('id', $unverifiedBusiness->id);

        $this->assertTrue($vItem['is_verified']);
        $this->assertFalse($uvItem['is_verified']);
    }
}
