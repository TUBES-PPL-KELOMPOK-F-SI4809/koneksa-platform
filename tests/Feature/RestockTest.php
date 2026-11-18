<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\Inventory\RestockService;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Exceptions;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class RestockTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_restock_and_server_controls_ledger_identity(): void
    {
        [$owner, $variant] = $this->inventory();
        $other = User::factory()->umkm()->create();
        $this->freezeTime();

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 7,
            'notes' => 'Produksi hari ini',
            'performed_by' => $other->id,
            'product_variant_id' => 999999,
            'type' => 'sale',
            'quantity_delta' => -100,
            'stock_quantity' => 999,
            'sale_id' => 999999,
        ])->assertRedirect(route('umkm.inventory.index'))->assertSessionHasNoErrors();

        $this->assertSame(17, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 1);
        $this->assertDatabaseHas('stock_movements', [
            'product_variant_id' => $variant->id,
            'performed_by' => $owner->id,
            'type' => 'restock',
            'quantity_delta' => 7,
            'notes' => 'Produksi hari ini',
            'sale_id' => null,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    public function test_restock_uses_current_balance_even_when_service_receives_a_stale_model(): void
    {
        [$owner, $variant] = $this->inventory();
        $service = app(RestockService::class);
        $service->restock($variant, $owner, 7, null);

        $service->restock($variant, $owner, 3, null);

        $this->assertSame(20, $variant->fresh()->stock_quantity);
        $this->assertSame([7, 3], StockMovement::orderBy('id')->pluck('quantity_delta')->all());
    }

    #[TestWith([null, 'Masukkan jumlah stok yang ditambahkan.'])]
    #[TestWith([0, 'Jumlah stok minimal 1.'])]
    #[TestWith([-1, 'Jumlah stok minimal 1.'])]
    #[TestWith([1.5, 'Jumlah stok harus berupa bilangan bulat.'])]
    #[TestWith(['abc', 'Jumlah stok harus berupa bilangan bulat.'])]
    #[TestWith([[1], 'Jumlah stok harus berupa bilangan bulat.'])]
    #[TestWith([2147483648, 'Jumlah stok melebihi batas yang dapat disimpan.'])]
    public function test_invalid_quantity_does_not_change_inventory(mixed $quantity, string $message): void
    {
        [$owner, $variant] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => $quantity,
        ])->assertSessionHasErrors(['quantity' => $message]);

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_invalid_notes_do_not_change_inventory(): void
    {
        [$owner, $variant] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1, 'notes' => str_repeat('a', 2001),
        ])->assertSessionHasErrors(['notes' => 'Catatan maksimal 2.000 karakter.']);

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_non_text_notes_are_rejected(): void
    {
        [$owner, $variant] = $this->inventory();

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1, 'notes' => ['invalid'],
        ])->assertSessionHasErrors(['notes' => 'Catatan harus berupa teks.']);

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_stock_can_reach_integer_limit_but_cannot_overflow(): void
    {
        [$owner, $variant] = $this->inventory();
        $variant->update(['stock_quantity' => 2147483646]);

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertSessionHasNoErrors();
        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertSessionHasErrors('quantity');

        $this->assertSame(2147483647, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_zero_stock_can_be_restocked(): void
    {
        [$owner, $variant] = $this->inventory();
        $variant->update(['stock_quantity' => 0]);

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertSessionHasNoErrors();

        $this->assertSame(1, $variant->fresh()->stock_quantity);
        $this->assertDatabaseHas('stock_movements', ['quantity_delta' => 1, 'notes' => null]);
    }

    public function test_guest_cannot_read_or_restock_inventory(): void
    {
        [, $variant] = $this->inventory();

        $this->get(route('umkm.inventory.index'))->assertRedirect(route('login'));
        $this->post(route('umkm.inventory.restocks.store', $variant), ['quantity' => 1])
            ->assertRedirect(route('login'));

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    #[TestWith(['mitra'])]
    #[TestWith(['pemda'])]
    #[TestWith(['admin'])]
    public function test_other_roles_cannot_read_or_restock_inventory(string $role): void
    {
        [, $variant] = $this->inventory();
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get(route('umkm.inventory.index'))->assertForbidden();
        $this->post(route('umkm.inventory.restocks.store', $variant), ['quantity' => 1])->assertForbidden();
        $this->assertFalse($user->can('restock', $variant));

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_disabled_owner_cannot_restock_inventory(): void
    {
        [$owner, $variant] = $this->inventory();
        $owner->update(['account_status' => User::STATUS_DISABLED]);

        $this->actingAs($owner)->postJson(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertForbidden();
        $this->assertFalse($owner->can('restock', $variant));

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_another_owner_cannot_restock_a_published_business_product(): void
    {
        [, $variant] = $this->inventory();
        $variant->product->business->update(['published_at' => now()]);
        $other = User::factory()->umkm()->create();

        $this->actingAs($other)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertForbidden();
        $this->assertFalse($other->can('restock', $variant));

        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    public function test_missing_variant_returns_not_found(): void
    {
        $owner = User::factory()->umkm()->create();

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', 999999), [
            'quantity' => 1,
        ])->assertNotFound();

        $this->assertDatabaseCount('stock_movements', 0);
    }

    #[TestWith(['product'])]
    #[TestWith(['variant'])]
    public function test_archived_inventory_cannot_be_restocked_but_history_remains_visible(string $target): void
    {
        [$owner, $variant] = $this->inventory();
        app(RestockService::class)->restock($variant, $owner, 2, 'Sebelum arsip');
        ($target === 'product' ? $variant->product : $variant)->update(['archived_at' => now()]);

        $this->actingAs($owner)->post(route('umkm.inventory.restocks.store', $variant), [
            'quantity' => 1,
        ])->assertSessionHasErrors('restock');
        $this->get(route('umkm.inventory.index'))->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Index')->has('variants.data', 0)->has('movements.data', 1));

        $this->assertSame(12, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 1);
    }

    public function test_inventory_and_history_are_scoped_to_owner_and_paginated(): void
    {
        [$owner, $variant] = $this->inventory();
        [$other, $foreign] = $this->inventory();
        $service = app(RestockService::class);
        $service->restock($foreign, $other, 1, 'Private other account');
        for ($i = 0; $i < 16; $i++) {
            $service->restock($variant, $owner, 1, 'Catatan '.$i);
        }

        $this->actingAs($owner)->get(route('umkm.inventory.index'))->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Index')->has('variants.data', 1)
            ->where('variants.data.0.id', $variant->id)
            ->where('variants.data.0.stock_quantity', 26)
            ->where('movements.total', 16)->has('movements.data', 15)
            ->where('movements.data.0.notes', 'Catatan 15')
            ->where('movements.data.0.performed_by', $owner->id));
        $this->get(route('umkm.inventory.index', ['history_page' => 2]))
            ->assertInertia(fn (Assert $page) => $page->has('movements.data', 1)
                ->where('movements.data.0.notes', 'Catatan 0'));
    }

    public function test_owner_without_business_sees_an_empty_state(): void
    {
        $owner = User::factory()->umkm()->create();

        $this->actingAs($owner)->get(route('umkm.inventory.index'))->assertInertia(fn (Assert $page) => $page
            ->component('Inventory/Index')->has('variants.data', 0)->has('movements.data', 0));
    }

    public function test_failed_ledger_insert_rolls_back_stock_and_returns_safe_feedback(): void
    {
        [$owner, $variant] = $this->inventory();
        Exceptions::fake([QueryException::class]);
        Event::listen('eloquent.creating: '.StockMovement::class, function (StockMovement $movement) {
            $movement->performed_by = 999999;
        });

        try {
            $this->actingAs($owner)->from(route('umkm.inventory.index'))
                ->post(route('umkm.inventory.restocks.store', $variant), ['quantity' => 5])
                ->assertRedirect(route('umkm.inventory.index'))
                ->assertSessionHasErrors(['restock' => 'Restock gagal disimpan. Silakan coba lagi.']);
        } finally {
            Event::forget('eloquent.creating: '.StockMovement::class);
        }

        Exceptions::assertReported(QueryException::class);
        $this->assertSame(10, $variant->fresh()->stock_quantity);
        $this->assertDatabaseCount('stock_movements', 0);
    }

    /** @return array{User, ProductVariant} */
    private function inventory(): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = Business::create([
            'user_id' => $owner->id, 'business_category_id' => $category->id, 'name' => 'Usaha '.$owner->id,
        ]);
        $product = Product::create(['business_id' => $business->id, 'name' => 'Kopi', 'unit' => 'bungkus']);
        $variant = $product->variants()->create([
            'variant_key' => 'default', 'selling_price' => 10000, 'stock_quantity' => 10,
        ]);

        return [$owner, $variant];
    }
}
