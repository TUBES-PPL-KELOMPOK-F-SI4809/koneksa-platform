<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedBusiness(string $name = 'Kopi Aroma Bandung', ?BusinessCategory $category = null): Business
    {
        $owner = User::factory()->umkm()->create();
        $category = $category ?? BusinessCategory::firstOrCreate(['name' => 'Kuliner']);

        return Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => $name,
            'description' => 'Produsen kopi terkemuka di Kota Bandung.',
            'address' => 'Jl. Braga No. 10',
            'district' => 'Sumur Bandung',
            'subdistrict' => 'Braga',
            'contact_phone' => '081234567890',
            'business_needs' => 'Perluasan distribusi ritel modern',
            'published_at' => now(),
        ]);
    }

    public function test_landing_page_is_accessible_by_guests_without_authentication_fr_33_1(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('canLogin')
            ->has('canRegister')
            ->has('stats')
            ->has('featured_businesses')
        );
    }

    public function test_landing_page_has_named_route_alias(): void
    {
        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
        );
    }

    public function test_landing_page_displays_aggregated_published_business_counts_and_featured_cards(): void
    {
        $cat = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $b1 = $this->createPublishedBusiness('Kopi Braga 1', $cat);
        $b2 = $this->createPublishedBusiness('Kopi Braga 2', $cat);

        // Buat UMKM belum terpublikasi (draft)
        $ownerDraft = User::factory()->umkm()->create();
        Business::create([
            'user_id' => $ownerDraft->id,
            'business_category_id' => $cat->id,
            'name' => 'UMKM Draft Rahasia',
            'published_at' => null,
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('stats.businesses_count', 2)
            ->where('stats.categories_count', 1)
            ->has('featured_businesses', 2)
            ->where('featured_businesses', fn ($list) => collect($list)->pluck('name')->contains('Kopi Braga 1') &&
                collect($list)->pluck('name')->contains('Kopi Braga 2')
            )
        );
    }

    public function test_landing_page_does_not_expose_private_business_data_or_financial_transactions(): void
    {
        $business = $this->createPublishedBusiness('Kopi Rahasia');

        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(function (Assert $page) {
            $page->component('Welcome');

            // Pastikan prop tidak memuat transaksi, data keuangan, email privat, atau proposal
            $featured = $page->toArray()['props']['featured_businesses'];
            $this->assertNotEmpty($featured);

            foreach ($featured as $item) {
                $this->assertArrayNotHasKey('financials', $item);
                $this->assertArrayNotHasKey('sales', $item);
                $this->assertArrayNotHasKey('transactions', $item);
                $this->assertArrayNotHasKey('proposals', $item);
                $this->assertArrayNotHasKey('password', $item);
                $this->assertArrayNotHasKey('email', $item);
            }
        });
    }

    public function test_authenticated_user_can_access_landing_page(): void
    {
        $user = User::factory()->umkm()->create(['name' => 'Budi Santoso']);

        $response = $this->actingAs($user)->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('auth.user.name', 'Budi Santoso')
        );
    }

    public function test_landing_page_gracefully_handles_empty_businesses_state(): void
    {
        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->where('stats.businesses_count', 0)
            ->has('featured_businesses', 0)
        );
    }

    public function test_landing_page_shows_verification_status_on_featured_businesses(): void
    {
        $verified = $this->createPublishedBusiness('Kopi Terverifikasi');
        $unverified = $this->createPublishedBusiness('Kopi Belum Verifikasi');

        DB::table('verification_requests')->insert([
            'business_id' => $verified->id,
            'submitted_data' => json_encode(['nib' => '1234567890']),
            'status' => 'approved',
            'reviewed_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk();
        $response->assertInertia(function (Assert $page) use ($verified, $unverified) {
            $featured = collect($page->toArray()['props']['featured_businesses']);
            $vItem = $featured->firstWhere('id', $verified->id);
            $uvItem = $featured->firstWhere('id', $unverified->id);

            $this->assertTrue($vItem['is_verified']);
            $this->assertFalse($uvItem['is_verified']);
        });
    }
}
