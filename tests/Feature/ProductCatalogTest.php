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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_product_with_default_variant_and_server_owned_business(): void
    {
        [$owner, $business] = $this->business();
        [, $foreign] = $this->business();
        $data = $this->payload();
        $data['business_id'] = $foreign->id;
        $data['archived_at'] = now();

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)
            ->assertRedirect(route('umkm.products.index'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['business_id' => $business->id, 'name' => 'Kopi Bandung', 'unit' => 'bungkus', 'archived_at' => null]);
        $variant = ProductVariant::sole();
        $this->assertSame('12500.50', $variant->selling_price);
        $this->assertSame('default', $variant->variant_key);
        $this->assertSame(0, $variant->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_updates_prices_and_adds_variant_without_replacing_stock_or_ids(): void
    {
        [$owner, $product, $variant] = $this->product();
        $data = $this->payload();
        $data['variants'][0]['id'] = $variant->id;
        $data['variants'][] = array_replace($this->payload()['variants'][0], ['variant_key' => 'besar', 'selling_price' => '20000']);

        $this->actingAs($owner)->put(route('umkm.products.update', $product), $data)->assertSessionHasNoErrors();

        $this->assertSame('Kopi Bandung', $product->fresh()->name);
        $this->assertSame('12500.50', $variant->fresh()->selling_price);
        $this->assertSame(8, $variant->fresh()->stock_quantity);
        $this->assertSame(2, $product->variants()->count());
        $this->assertSame(0, $product->variants()->where('variant_key', 'besar')->sole()->stock_quantity);
    }

    public function test_missing_or_foreign_variant_ids_are_rejected_atomically(): void
    {
        [$owner, $product, $variant] = $this->product();
        [, , $foreign] = $this->product();
        $data = $this->payload();
        $data['variants'][0]['id'] = $foreign->id;

        $this->actingAs($owner)->put(route('umkm.products.update', $product), $data)->assertSessionHasErrors('variants');

        $this->assertSame('Kopi awal', $product->fresh()->name);
        $this->assertSame('10000.00', $variant->fresh()->selling_price);
        $this->assertSame('10000.00', $foreign->fresh()->selling_price);
    }

    public function test_stale_form_cannot_remove_an_existing_variant(): void
    {
        [$owner, $product] = $this->product();

        $this->actingAs($owner)->put(route('umkm.products.update', $product), $this->payload())->assertSessionHasErrors('variants');

        $this->assertDatabaseCount('product_variants', 1);
        $this->assertSame('Kopi awal', $product->fresh()->name);
    }

    public function test_existing_variant_key_is_not_renamed_and_changes_roll_back(): void
    {
        [$owner, $product, $variant] = $this->product();
        $data = $this->payload();
        $data['variants'][0]['id'] = $variant->id;
        $data['variants'][0]['variant_key'] = 'renamed';

        $this->actingAs($owner)->put(route('umkm.products.update', $product), $data)->assertSessionHasErrors('variants.0.variant_key');

        $this->assertSame('Kopi awal', $product->fresh()->name);
        $this->assertSame('default', $variant->fresh()->variant_key);
    }

    #[TestWith(['-1'])]
    #[TestWith(['1.234'])]
    #[TestWith(['10000000000000'])]
    #[TestWith(['1e3'])]
    #[TestWith([['bad']])]
    public function test_invalid_prices_are_rejected_without_creating_product(mixed $price): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data['variants'][0]['selling_price'] = $price;

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasErrors([
            'variants.0.selling_price' => 'Harga harus nol atau positif, maksimal 13 digit dan 2 angka desimal.',
        ]);

        $this->assertDatabaseCount('products', 0);
    }

    #[TestWith(['0'])]
    #[TestWith(['9999999999999.99'])]
    public function test_price_boundaries_can_be_saved(string $price): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data['variants'][0]['selling_price'] = $price;

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasNoErrors();

        $this->assertDatabaseCount('product_variants', 1);
    }

    public function test_required_fields_and_nested_variants_are_validated(): void
    {
        [$owner] = $this->business();

        $this->actingAs($owner)->post(route('umkm.products.store'), [])->assertSessionHasErrors([
            'name' => 'Nama produk wajib diisi.', 'unit' => 'Satuan wajib diisi.', 'variants' => 'Varian wajib diisi.',
        ]);

        $this->assertDatabaseCount('products', 0);
    }

    public function test_duplicate_variant_names_are_rejected_case_insensitively(): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data['variants'][] = array_replace($data['variants'][0], ['variant_key' => 'DEFAULT']);

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasErrors('variants.1.variant_key');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_unknown_variant_attributes_cannot_set_stock_or_ownership(): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data['variants'][0]['stock_quantity'] = 100;

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasErrors('variants.0');

        $this->assertDatabaseCount('products', 0);
    }

    #[TestWith(['name', 151])]
    #[TestWith(['unit', 51])]
    #[TestWith(['description', 10001])]
    public function test_text_lengths_match_storage_boundaries(string $field, int $length): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data[$field] = str_repeat('x', $length);

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasErrors($field);

        $this->assertDatabaseCount('products', 0);
    }

    #[TestWith([-1])]
    #[TestWith([1.5])]
    #[TestWith([2147483648])]
    public function test_invalid_stock_threshold_is_rejected(mixed $threshold): void
    {
        [$owner] = $this->business();
        $data = $this->payload();
        $data['variants'][0]['low_stock_threshold'] = $threshold;

        $this->actingAs($owner)->post(route('umkm.products.store'), $data)->assertSessionHasErrors('variants.0.low_stock_threshold');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_another_owner_cannot_edit_or_archive_product(): void
    {
        [, $product, $variant] = $this->product();
        [$other] = $this->business();

        $this->actingAs($other)->put(route('umkm.products.update', $product), $this->payload())->assertNotFound();
        $this->post(route('umkm.products.archive', $product))->assertNotFound();

        $this->assertNull($product->fresh()->archived_at);
        $this->assertSame('10000.00', $variant->fresh()->selling_price);
    }

    public function test_guest_cannot_list_create_update_or_archive(): void
    {
        [, $product] = $this->product();

        $this->get(route('umkm.products.index'))->assertRedirect(route('login'));
        $this->post(route('umkm.products.store'), $this->payload())->assertRedirect(route('login'));
        $this->put(route('umkm.products.update', $product), $this->payload())->assertRedirect(route('login'));
        $this->post(route('umkm.products.archive', $product))->assertRedirect(route('login'));
    }

    #[TestWith(['mitra'])]
    #[TestWith(['pemda'])]
    #[TestWith(['admin'])]
    public function test_other_roles_cannot_access_catalog(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get(route('umkm.products.index'))->assertForbidden();
        $this->post(route('umkm.products.store'), $this->payload())->assertForbidden();
        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Product::class));
    }

    public function test_user_without_business_can_read_empty_state_but_cannot_create(): void
    {
        $owner = User::factory()->umkm()->create();

        $this->actingAs($owner)->get(route('umkm.products.index'))->assertInertia(fn (Assert $page) => $page
            ->component('Products/Index')->where('hasBusiness', false)->has('products.data', 0));
        $this->post(route('umkm.products.store'), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('products', 0);
    }

    #[TestWith(['email_verified_at', null])]
    #[TestWith(['must_change_password', true])]
    public function test_incomplete_account_cannot_manage_catalog(string $attribute, mixed $value): void
    {
        [$owner] = $this->business();
        $owner->forceFill([$attribute => $value])->save();

        $this->actingAs($owner)->get(route('umkm.products.index'))->assertForbidden();
        $this->post(route('umkm.products.store'), $this->payload())->assertForbidden();
    }

    public function test_disabled_account_cannot_write(): void
    {
        [$owner] = $this->business();
        $owner->update(['account_status' => 'disabled']);

        $this->actingAs($owner)->postJson(route('umkm.products.store'), $this->payload())->assertForbidden();

        $this->assertDatabaseCount('products', 0);
    }

    public function test_lists_only_owned_products_with_search_archive_filters_and_pagination(): void
    {
        [$owner, $product] = $this->product();
        $product->update(['name' => 'Kopi pilihan']);
        [, $archived] = $this->product($owner);
        $archived->update(['archived_at' => now()]);
        $this->product();

        $this->actingAs($owner)->get(route('umkm.products.index'))->assertInertia(fn (Assert $page) => $page
            ->where('products.total', 1)->where('products.data.0.id', $product->id)
            ->where('counts.active', 1)->where('counts.archived', 1));
        $this->get(route('umkm.products.index', ['status' => 'archived']))->assertInertia(fn (Assert $page) => $page
            ->where('products.total', 1)->where('products.data.0.id', $archived->id));
        $this->get(route('umkm.products.index', ['status' => 'all', 'search' => 'pilihan']))->assertInertia(fn (Assert $page) => $page->where('products.total', 1));
        for ($i = 0; $i < 13; $i++) {
            $this->product($owner);
        }
        $this->get(route('umkm.products.index', ['page' => 2]))->assertInertia(fn (Assert $page) => $page->has('products.data', 2));
    }

    public function test_archive_preserves_sale_snapshots_stock_and_ledger_and_blocks_operational_use(): void
    {
        [$owner, $product, $variant] = $this->product();
        $this->freezeTime();
        $saleId = DB::table('sales')->insertGetId(['business_id' => $product->business_id, 'invoice_number' => 'INV-HISTORY', 'sold_at' => now(), 'status' => 'recorded', 'total_amount' => '10000.00', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('sale_items')->insert(['sale_id' => $saleId, 'product_variant_id' => $variant->id, 'product_name_snapshot' => 'Kopi historis', 'variant_label_snapshot' => 'Standar', 'unit_snapshot' => 'bungkus', 'quantity' => 1, 'unit_price' => 10000, 'created_at' => now(), 'updated_at' => now()]);
        $movement = StockMovement::create(['product_variant_id' => $variant->id, 'performed_by' => $owner->id, 'type' => 'restock', 'quantity_delta' => 8]);
        $this->assertTrue(Gate::forUser($owner)->allows('sell', $variant));

        $this->actingAs($owner)->post(route('umkm.products.archive', $product))->assertSessionHasNoErrors();

        $this->assertNotNull($product->fresh()->archived_at);
        $this->assertSame(8, $variant->fresh()->stock_quantity);
        $this->assertModelExists($movement);
        $this->assertDatabaseHas('sale_items', ['sale_id' => $saleId, 'product_name_snapshot' => 'Kopi historis', 'unit_price' => 10000]);
        $this->assertFalse(Gate::forUser($owner)->allows('sell', $variant->fresh()));
        $this->assertSame(0, ProductVariant::availableForSale()->count());
        $this->post(route('umkm.inventory.restocks.store', $variant), ['quantity' => 1])->assertSessionHasErrors('restock');
        $this->get(route('umkm.inventory.index'))->assertInertia(fn (Assert $page) => $page->has('variants.data', 0)->has('movements.data', 1));
        $this->put(route('umkm.products.update', $product), $this->payload())->assertSessionHasErrors('product');
        $timestamp = $product->fresh()->archived_at->toISOString();
        $this->travel(1)->hour();
        $this->post(route('umkm.products.archive', $product))->assertSessionHasNoErrors();
        $this->assertSame($timestamp, $product->fresh()->archived_at->toISOString());
    }

    public function test_archived_variant_and_foreign_owner_are_not_eligible_for_sale(): void
    {
        [$owner, , $variant] = $this->product();
        $other = User::factory()->umkm()->create();
        $this->assertFalse(Gate::forUser($other)->allows('sell', $variant));
        $variant->update(['archived_at' => now()]);

        $this->assertFalse(Gate::forUser($owner)->allows('sell', $variant));
        $this->assertSame(0, ProductVariant::availableForSale()->count());
    }

    public function test_variant_insert_failure_rolls_back_product_and_returns_safe_feedback(): void
    {
        [$owner] = $this->business();
        Exceptions::fake([QueryException::class]);
        Event::listen('eloquent.creating: '.ProductVariant::class, function (ProductVariant $variant) {
            $variant->product_id = 999999;
        });

        try {
            $this->actingAs($owner)->from(route('umkm.products.index'))->post(route('umkm.products.store'), $this->payload())
                ->assertSessionHasErrors(['product' => 'Produk gagal disimpan. Silakan coba lagi.']);
        } finally {
            Event::forget('eloquent.creating: '.ProductVariant::class);
        }

        Exceptions::assertReported(QueryException::class);
        $this->assertDatabaseCount('products', 0);
        $this->assertDatabaseCount('product_variants', 0);
    }

    private function payload(): array
    {
        return ['name' => 'Kopi Bandung', 'description' => 'Kopi pilihan Bandung', 'unit' => 'bungkus', 'variants' => [
            ['id' => null, 'variant_key' => 'default', 'selling_price' => '12500.50', 'low_stock_threshold' => 2, 'sku' => null, 'size' => null, 'color' => null],
        ]];
    }

    private function business(): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = Business::create(['user_id' => $owner->id, 'business_category_id' => $category->id, 'name' => 'Usaha '.$owner->id]);

        return [$owner, $business];
    }

    private function product(?User $owner = null): array
    {
        if (! $owner) {
            [$owner, $business] = $this->business();
        } else {
            $business = $owner->business;
        }
        $product = Product::create(['business_id' => $business->id, 'name' => 'Kopi awal', 'unit' => 'bungkus']);
        $variant = $product->variants()->create(['variant_key' => 'default', 'selling_price' => '10000.00', 'stock_quantity' => 8]);

        return [$owner, $product, $variant];
    }
}
