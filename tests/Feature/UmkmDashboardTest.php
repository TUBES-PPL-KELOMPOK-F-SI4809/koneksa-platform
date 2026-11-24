<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UmkmDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_aggregates_total_revenue_and_sales_count(): void
    {
        [$owner, $business] = $this->setupUmkm();

        Sale::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-TEST-001',
            'sold_at' => now(),
            'status' => 'recorded',
            'total_amount' => '150000.00',
        ]);

        Sale::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-TEST-002',
            'sold_at' => now(),
            'status' => 'recorded',
            'total_amount' => '75000.00',
        ]);

        $response = $this->actingAs($owner)->get(route('dashboard'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Umkm/Dashboard')
            ->where('financials.totalRevenue', fn ($val) => (float) $val === 225000.0)
            ->where('financials.totalSalesCount', 2)
            ->has('salesTrend', 7)
        );
    }

    public function test_dashboard_revenue_is_isolated_to_authenticated_business_owner(): void
    {
        [$owner1, $business1] = $this->setupUmkm();
        [$owner2, $business2] = $this->setupUmkm();

        Sale::create([
            'business_id' => $business1->id,
            'invoice_number' => 'INV-OWNER1',
            'sold_at' => now(),
            'status' => 'recorded',
            'total_amount' => '50000.00',
        ]);

        Sale::create([
            'business_id' => $business2->id,
            'invoice_number' => 'INV-OWNER2',
            'sold_at' => now(),
            'status' => 'recorded',
            'total_amount' => '200000.00',
        ]);

        $this->actingAs($owner1)->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('financials.totalRevenue', fn ($val) => (float) $val === 50000.0)
                ->where('financials.totalSalesCount', 1)
            );
    }

    private function setupUmkm(): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => 'Usaha '.$owner->id,
        ]);

        $product = Product::create([
            'business_id' => $business->id,
            'name' => 'Produk '.$owner->id,
            'unit' => 'pcs',
        ]);

        $product->variants()->create([
            'variant_key' => 'default',
            'selling_price' => '10000.00',
            'stock_quantity' => 10,
            'low_stock_threshold' => 2,
        ]);

        return [$owner, $business];
    }
}
