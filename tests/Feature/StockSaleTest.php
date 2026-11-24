<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class StockSaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_uses_server_prices_and_atomically_records_items_stock_and_ledger(): void
    {
        [$owner, $variant] = $this->inventory();
        $second = $variant->product->variants()->create(['variant_key' => 'kecil', 'selling_price' => '0.10', 'stock_quantity' => 5]);
        $this->freezeTime();

        $response = $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), [
            'items' => [['product_variant_id' => $variant->id, 'quantity' => 3], ['product_variant_id' => $second->id, 'quantity' => 1]],
            'notes' => 'Penjualan kasir', 'total_amount' => 1, 'business_id' => 999999, 'status' => 'cancelled',
        ])->assertCreated()->assertJsonPath('sale.total_amount', '37501.75')->assertJsonPath('sale.status', 'recorded')
            ->assertJsonPath('sale.business_id', $variant->product->business_id)->assertJsonCount(2, 'sale.items');
        $saleId = $response->json('sale.id');
        $this->assertSame(7, $variant->fresh()->stock_quantity);
        $this->assertSame(4, $second->fresh()->stock_quantity);
        $this->assertDatabaseHas('sale_items', ['sale_id' => $saleId, 'product_variant_id' => $variant->id, 'quantity' => 3,
            'unit_price' => '12500.55', 'product_name_snapshot' => 'Kopi Bandung', 'variant_label_snapshot' => 'default', 'unit_snapshot' => 'bungkus']);
        $this->assertDatabaseHas('stock_movements', ['sale_id' => $saleId, 'product_variant_id' => $variant->id, 'performed_by' => $owner->id, 'type' => 'sale', 'quantity_delta' => -3]);
        $this->assertDatabaseCount('stock_movements', 2);
    }

    public function test_exact_remaining_stock_can_be_sold_and_warning_becomes_out_of_stock(): void
    {
        [$owner, $variant] = $this->inventory();
        $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 10))->assertCreated();

        $this->assertSame(0, $variant->fresh()->stock_quantity);
        $this->get(route('dashboard'))->assertInertia(fn (Assert $page) => $page->where('counts.out', 1)->where('lowStockVariants.0.stock_status', 'out'));
        $this->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertUnprocessable()->assertJsonValidationErrors('items.0.quantity');
        $this->assertDatabaseCount('sales', 1);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertSame(0, $variant->fresh()->stock_quantity);
    }

    public function test_insufficient_stock_for_one_item_cancels_the_entire_cart(): void
    {
        [$owner, $variant] = $this->inventory();
        $second = $variant->product->variants()->create(['variant_key' => 'besar', 'selling_price' => 1000, 'stock_quantity' => 1]);
        $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), ['items' => [
            ['product_variant_id' => $variant->id, 'quantity' => 2], ['product_variant_id' => $second->id, 'quantity' => 2],
        ]])->assertUnprocessable()->assertJsonValidationErrors('items.1.quantity');

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertSame(1, $second->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_invalid_quantities_duplicates_and_client_prices_are_rejected_without_mutation(): void
    {
        [$owner, $variant] = $this->inventory();
        foreach ([0, -1, 1.5, 'abc', 2147483648] as $quantity) {
            $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, $quantity))
                ->assertUnprocessable()->assertJsonValidationErrors('items.0.quantity');
        }
        $item = ['product_variant_id' => $variant->id, 'quantity' => 1];
        foreach ([[], [$item, $item], [[...$item, 'unit_price' => 1]], array_fill(0, 51, $item)] as $items) {
            $this->postJson(route('umkm.inventory.sales.store'), ['items' => $items])->assertUnprocessable();
        }
        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_sale_endpoint_requires_an_eligible_owner_with_a_business(): void
    {
        [$owner, $variant] = $this->inventory();
        $this->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertUnauthorized();
        foreach (['mitra', 'pemda', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
        }
        foreach ([['account_status' => 'disabled'], ['must_change_password' => true], ['email_verified_at' => null]] as $attributes) {
            $owner->forceFill($attributes)->save();
            $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
            $owner->forceFill(['account_status' => 'enabled', 'must_change_password' => false, 'email_verified_at' => now()])->save();
        }
        $this->actingAs(User::factory()->umkm()->create())->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
    }

    public function test_foreign_archived_and_missing_variants_cannot_be_sold(): void
    {
        [$owner, $variant] = $this->inventory();
        [$other] = $this->inventory();
        $this->actingAs($other)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
        $variant->update(['archived_at' => now()]);
        $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
        $variant->update(['archived_at' => null]);
        $variant->product->update(['archived_at' => now()]);
        $this->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertForbidden();
        $this->postJson(route('umkm.inventory.sales.store'), ['items' => [['product_variant_id' => 999999, 'quantity' => 1]]])->assertNotFound();
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
    }

    public function test_ledger_failure_rolls_back_sale_items_and_all_stock_changes(): void
    {
        [$owner, $variant] = $this->inventory();
        $second = $variant->product->variants()->create(['variant_key' => 'besar', 'selling_price' => 1000, 'stock_quantity' => 5]);
        Exceptions::fake([QueryException::class]);
        $created = 0;
        Event::listen('eloquent.creating: '.StockMovement::class, function (StockMovement $movement) use (&$created) {
            if (++$created === 2) {
                $movement->performed_by = 999999;
            }
        });
        try {
            $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), ['items' => [
                ['product_variant_id' => $variant->id, 'quantity' => 1], ['product_variant_id' => $second->id, 'quantity' => 1],
            ]])->assertInternalServerError()->assertJsonPath('message', 'Penjualan gagal disimpan. Silakan coba lagi.');
        } finally {
            Event::forget('eloquent.creating: '.StockMovement::class);
        }
        Exceptions::assertReported(QueryException::class);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertSame(5, $second->fresh()->stock_quantity);
        $this->assertDatabaseCount('sales', 0);
        $this->assertDatabaseCount('sale_items', 0);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_total_overflow_is_rejected_and_zero_price_sales_are_supported(): void
    {
        [$owner, $variant] = $this->inventory();
        $variant->update(['selling_price' => '9999999999999.99']);
        $this->actingAs($owner)->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 2))->assertUnprocessable()->assertJsonValidationErrors('items');
        $this->assertDatabaseCount('sales', 0);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $variant->update(['selling_price' => 0]);
        $this->postJson(route('umkm.inventory.sales.store'), $this->payload($variant, 1))->assertCreated()->assertJsonPath('sale.total_amount', '0.00');
        $this->assertSame(9, $variant->fresh()->stock_quantity);
    }

    private function payload(ProductVariant $variant, mixed $quantity): array
    {
        return ['items' => [['product_variant_id' => $variant->id, 'quantity' => $quantity]]];
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
