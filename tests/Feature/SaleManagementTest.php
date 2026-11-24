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

class SaleManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_create_screen_can_be_rendered_with_available_variants(): void
    {
        [$owner, $variant] = $this->inventory();

        $response = $this->actingAs($owner)->get(route('umkm.sales.create'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Sales/Create')
            ->has('variants', 1)
            ->where('variants.0.id', $variant->id)
            ->where('hasBusiness', true)
        );
    }

    public function test_sale_can_be_stored_and_redirects_to_sales_index(): void
    {
        [$owner, $variant] = $this->inventory();

        $response = $this->actingAs($owner)->post(route('umkm.sales.store'), [
            'items' => [
                ['product_variant_id' => $variant->id, 'quantity' => 2],
            ],
            'notes' => 'Penjualan kasir via form',
        ]);

        $response->assertRedirect(route('umkm.sales.index'));
        $response->assertSessionHas('status');

        $this->assertSame(8, $variant->fresh()->stock_quantity);
        $this->assertDatabaseHas('sales', [
            'business_id' => $variant->product->business_id,
            'status' => 'recorded',
        ]);
        $this->assertDatabaseHas('sale_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'quantity_delta' => -2,
            'type' => 'sale',
        ]);
    }

    public function test_sales_index_screen_renders_sales_history_scoped_to_owner(): void
    {
        [$owner, $variant] = $this->inventory();
        [$otherOwner, $otherVariant] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.sales.store'), [
            'items' => [['product_variant_id' => $variant->id, 'quantity' => 1]],
        ]);

        $this->actingAs($otherOwner)->post(route('umkm.sales.store'), [
            'items' => [['product_variant_id' => $otherVariant->id, 'quantity' => 2]],
        ]);

        $response = $this->actingAs($owner)->get(route('umkm.sales.index'));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Sales/Index')
            ->has('sales.data', 1)
            ->where('sales.total', 1)
            ->where('sales.data.0.business_id', $variant->product->business_id)
        );
    }

    public function test_sale_show_screen_renders_details_for_owner(): void
    {
        [$owner, $variant] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.sales.store'), [
            'items' => [['product_variant_id' => $variant->id, 'quantity' => 3]],
            'notes' => 'Catatan nota',
        ]);

        $sale = Sale::firstOrFail();

        $response = $this->actingAs($owner)->get(route('umkm.sales.show', $sale));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Sales/Show')
            ->where('sale.id', $sale->id)
            ->where('sale.invoice_number', $sale->invoice_number)
            ->has('sale.items', 1)
        );
    }

    public function test_user_cannot_view_other_users_sale_detail(): void
    {
        [$owner, $variant] = $this->inventory();
        [$otherOwner] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.sales.store'), [
            'items' => [['product_variant_id' => $variant->id, 'quantity' => 1]],
        ]);

        $sale = Sale::firstOrFail();

        $this->actingAs($otherOwner)->get(route('umkm.sales.show', $sale))->assertForbidden();
    }

    private function inventory(): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = Business::create(['user_id' => $owner->id, 'business_category_id' => $category->id, 'name' => 'Usaha '.$owner->id]);
        $product = Product::create(['business_id' => $business->id, 'name' => 'Kopi Bandung', 'unit' => 'bungkus']);
        $variant = $product->variants()->create(['variant_key' => 'default', 'selling_price' => '12500.55', 'stock_quantity' => 10, 'low_stock_threshold' => 2]);

        return [$owner, $variant];
    }
}
