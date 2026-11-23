<?php

namespace Tests\Feature;

use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class LowStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_status_and_low_stock_query_include_the_threshold_and_zero_stock(): void
    {
        $owner = User::factory()->umkm()->create();
        foreach ([[0, 0, 'out'], [1, 0, 'healthy'], [2, 2, 'low'], [1, 2, 'low'], [3, 2, 'healthy']] as [$stock, $threshold, $status]) {
            $variant = $this->variant($owner, $stock, $threshold);
            $this->assertSame($status, $variant->stock_status);
            $this->assertSame(in_array($status, ['out', 'low']), ProductVariant::lowStock()->whereKey($variant->id)->exists());
        }
    }

    public function test_dashboard_counts_only_owned_active_variants_and_limits_warning_list(): void
    {
        $owner = User::factory()->umkm()->create();
        $out = $this->variant($owner, 0, 0);
        for ($index = 1; $index <= 6; $index++) {
            $this->variant($owner, $index, 6);
        }
        $this->variant($owner, 10, 2);
        $archivedProduct = $this->variant($owner, 0, 2);
        $archivedProduct->product->update(['archived_at' => now()]);
        $archivedVariant = $this->variant($owner, 0, 2);
        $archivedVariant->update(['archived_at' => now()]);
        $this->variant(User::factory()->umkm()->create(), 0, 2);

        foreach (['dashboard', 'umkm.dashboard'] as $route) {
            $this->actingAs($owner)->get(route($route))->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component('Umkm/Dashboard')->where('counts.active', 8)->where('counts.low', 7)->where('counts.out', 1)
                ->has('lowStockVariants', 5)->where('lowStockVariants.0.id', $out->id)
                ->where('lowStockVariants.0.stock_status', 'out')->where('hasBusiness', true));
        }
        $this->assertFalse(ProductVariant::lowStock()->whereKey($archivedProduct->id)->exists());
        $this->assertFalse(ProductVariant::lowStock()->whereKey($archivedVariant->id)->exists());
    }

    public function test_catalog_and_inventory_receive_consistent_status_and_restock_removes_warning(): void
    {
        $owner = User::factory()->umkm()->create();
        $variant = $this->variant($owner, 2, 2);

        $this->actingAs($owner)->get(route('umkm.products.index'))->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.variants.0.stock_status', 'low'));
        $this->get(route('umkm.inventory.index'))->assertInertia(fn (Assert $page) => $page
            ->where('variants.data.0.stock_status', 'low')->where('variants.data.0.low_stock_threshold', 2));
        $this->post(route('umkm.inventory.restocks.store', $variant), ['quantity' => 1])->assertSessionHasNoErrors();
        $this->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page
            ->where('counts.low', 0)->where('counts.out', 0)->has('lowStockVariants', 0));
        $this->get(route('umkm.products.index'))->assertInertia(fn (Assert $page) => $page
            ->where('products.data.0.variants.0.stock_status', 'healthy'));
    }

    public function test_dashboard_without_business_is_empty_and_other_roles_keep_their_dashboard(): void
    {
        $this->actingAs(User::factory()->umkm()->create())->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page
            ->component('Umkm/Dashboard')->where('counts.active', 0)->where('hasBusiness', false)->has('lowStockVariants', 0));
        foreach (['mitra', 'pemda', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard')->where('role', $role)->missing('lowStockVariants'));
        }
    }

    public function test_warning_dashboard_rejects_accounts_not_eligible_to_manage_products(): void
    {
        foreach ([['email_verified_at' => null], ['must_change_password' => true]] as $attributes) {
            $this->actingAs(User::factory()->umkm()->create($attributes))->get(route('umkm.dashboard'))->assertForbidden();
        }
    }

    private function variant(User $owner, int $stock, int $threshold): ProductVariant
    {
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = $owner->business()->firstOrCreate([], ['business_category_id' => $category->id, 'name' => 'Usaha '.$owner->id]);
        $product = Product::create(['business_id' => $business->id, 'name' => 'Produk '.$stock, 'unit' => 'bungkus']);

        return $product->variants()->create([
            'variant_key' => 'default', 'selling_price' => '10000.00',
            'stock_quantity' => $stock, 'low_stock_threshold' => $threshold,
        ]);
    }
}
