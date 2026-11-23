<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BusinessProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_save_needs_publish_idempotently_and_return_to_draft(): void
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::create(['name' => 'Kuliner']);
        $data = ['name' => 'Usaha Saya', 'business_category_id' => $category->id, 'business_needs' => 'Mencari mitra distribusi'];
        $this->actingAs($owner)->put(route('business-profile.update'), $data)
            ->assertRedirect(route('business-profile.edit'))->assertSessionHasNoErrors();
        $business = $owner->business()->firstOrFail();
        $this->assertNull($business->published_at);
        $this->assertSame('Mencari mitra distribusi', $business->business_needs);

        $this->travelTo(now()->startOfSecond());
        $publishedAt = now();
        $this->put(route('business-profile.update'), [...$data, 'is_published' => true])
            ->assertRedirect(route('business-profile.edit'))->assertSessionHasNoErrors();
        $this->assertTrue($publishedAt->equalTo($business->refresh()->published_at));
        $this->travel(1)->hours();
        $this->put(route('business-profile.update'), [...$data, 'business_needs' => 'Pendanaan mesin', 'is_published' => true])
            ->assertSessionHasNoErrors();
        $this->assertTrue($publishedAt->equalTo($business->refresh()->published_at));
        $this->get(route('business-profile.edit'))->assertInertia(fn (Assert $page) => $page
            ->where('business.business_needs', 'Pendanaan mesin')->where('business.published_at', $publishedAt->toJSON()));

        $this->put(route('business-profile.update'), [...$data, 'business_needs' => '', 'is_published' => false])
            ->assertSessionHasNoErrors();
        $this->assertNull($business->refresh()->published_at);
        $this->assertNull($business->business_needs);
        $this->assertDatabaseCount('businesses', 1);
    }

    public function test_new_business_can_be_published_on_first_save(): void
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::create(['name' => 'Kuliner']);

        $this->actingAs($owner)->put(route('business-profile.update'), [
            'name' => 'Usaha Baru', 'business_category_id' => $category->id,
            'business_needs' => 'Perluasan pasar', 'is_published' => true,
        ])->assertRedirect(route('business-profile.edit'))->assertSessionHasNoErrors();

        $this->assertNotNull($owner->business()->firstOrFail()->published_at);
        $this->assertDatabaseHas('businesses', ['user_id' => $owner->id, 'business_needs' => 'Perluasan pasar']);
    }

    public function test_invalid_needs_or_publication_leave_existing_profile_unchanged(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublicationProfile($owner);
        $data = $business->only(['name', 'business_category_id']);

        foreach ([
            [['business_needs' => ['invalid']], 'Kebutuhan pengembangan usaha harus berupa teks.'],
            [['business_needs' => str_repeat('a', 10001)], 'Kebutuhan pengembangan usaha tidak boleh lebih dari 10000 karakter.'],
            [['is_published' => 'invalid'], 'Status publikasi harus berupa pilihan publik atau draft.'],
            [['is_published' => null], 'Status publikasi wajib diisi.'],
        ] as [$invalid, $message]) {
            $this->actingAs($owner)->put(route('business-profile.update'), [...$data, ...$invalid])
                ->assertSessionHasErrors([array_key_first($invalid) => $message]);
            $this->assertDatabaseHas('businesses', ['id' => $business->id, 'business_needs' => 'Kebutuhan awal', 'published_at' => null]);
        }
    }

    public function test_verified_identity_allows_needs_and_publication_changes_but_rejects_identity_changes_atomically(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublicationProfile($owner);
        DB::table('verification_requests')->insert(['business_id' => $business->id, 'submitted_data' => '{}', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        $data = [...$business->only(['name', 'business_category_id']), 'business_needs' => 'Produksi', 'is_published' => true];

        $this->actingAs($owner)->put(route('business-profile.update'), [...$data, 'name' => 'Diubah'])
            ->assertSessionHasErrors('name');
        $this->assertDatabaseHas('businesses', ['id' => $business->id, 'business_needs' => 'Kebutuhan awal', 'published_at' => null]);
        $this->put(route('business-profile.update'), $data)->assertSessionHasNoErrors();
        $this->assertSame('Produksi', $business->refresh()->business_needs);
        $this->assertNotNull($business->published_at);
    }

    public function test_payload_cannot_change_another_business_or_forge_publication_time(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublicationProfile($owner);
        $other = User::factory()->umkm()->create();
        $otherBusiness = $this->createPublicationProfile($other);
        $data = [...$business->only(['name', 'business_category_id']), 'business_needs' => 'Distribusi',
            'user_id' => $other->id, 'id' => $otherBusiness->id, 'business_id' => $otherBusiness->id, 'published_at' => '2000-01-01 00:00:00'];

        $this->actingAs($owner)->put(route('business-profile.update'), $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('businesses', ['id' => $business->id, 'user_id' => $owner->id, 'business_needs' => 'Distribusi', 'published_at' => null]);
        $this->assertDatabaseHas('businesses', ['id' => $otherBusiness->id, 'business_needs' => 'Kebutuhan awal', 'published_at' => null]);
        $this->putJson(route('umkm.business.update', $otherBusiness), ['business_needs' => 'Dibajak', 'is_published' => true])->assertForbidden();
        $this->put(route('business-profile.update'), [...$data, 'is_published' => true])->assertSessionHasNoErrors();
        $publishedAt = $business->refresh()->published_at;
        $this->assertNotNull($publishedAt);
        $this->assertTrue($publishedAt->isAfter('2000-01-01'));
        $this->put(route('business-profile.update'), $data)->assertSessionHasNoErrors();
        $this->assertTrue($publishedAt->equalTo($business->refresh()->published_at));
    }

    public function test_other_readers_only_receive_public_fields_and_lose_access_when_unpublished(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublicationProfile($owner);
        $other = User::factory()->umkm()->create();
        $business->update(['published_at' => now()]);

        $this->actingAs($other)->getJson(route('umkm.business.show', $business))
            ->assertOk()->assertExactJson($business->only([
                'id', 'business_category_id', 'name', 'description', 'address', 'district',
                'subdistrict', 'contact_phone', 'logo_path', 'business_needs',
            ]));
        $this->actingAs($owner)->getJson(route('umkm.business.show', $business))->assertOk()->assertJsonPath('user_id', $owner->id);
        $this->put(route('business-profile.update'), [...$business->only(['name', 'business_category_id']), 'is_published' => false])->assertSessionHasNoErrors();
        $this->actingAs($other)->getJson(route('umkm.business.show', $business))->assertForbidden();
    }

    public function test_published_profiles_of_disabled_or_deleted_owners_are_not_visible_to_other_accounts(): void
    {
        $owner = User::factory()->umkm()->create();
        $business = $this->createPublicationProfile($owner);
        $business->update(['published_at' => now()]);
        $other = User::factory()->umkm()->create();
        $owner->update(['account_status' => 'disabled']);

        $this->actingAs($other)->getJson(route('umkm.business.show', $business))->assertForbidden();
        $owner->update(['account_status' => 'enabled']);
        $owner->delete();
        $this->getJson(route('umkm.business.show', $business))->assertForbidden();
    }

    private function createPublicationProfile(User $owner): Business
    {
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);

        return Business::create([
            'user_id' => $owner->id, 'business_category_id' => $category->id,
            'name' => 'Usaha '.$owner->id, 'business_needs' => 'Kebutuhan awal',
        ]);
    }

    public function test_database_prevents_a_second_business_for_the_same_user(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role' => 'umkm']);
        $business = ['user_id' => $user->id, 'business_category_id' => $category, 'name' => 'Usaha Pertama', 'created_at' => now(), 'updated_at' => now()];
        DB::table('businesses')->insert($business);

        $this->expectException(QueryException::class);
        DB::table('businesses')->insert([...$business, 'name' => 'Usaha Kedua']);
    }

    public function test_umkm_can_create_and_update_only_their_own_business(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $owner = User::factory()->create(['role' => 'umkm'])->refresh();
        $other = User::factory()->create(['role' => 'umkm'])->refresh();
        $data = ['name' => 'Usaha Saya', 'business_category_id' => $category, 'address' => 'Bandung', 'user_id' => $other->id];

        $this->actingAs($owner)->put(route('business-profile.update'), $data)->assertRedirect(route('business-profile.edit'))->assertSessionHasNoErrors();
        $this->actingAs($owner)->put(route('business-profile.update'), [...$data, 'name' => 'Nama Baru'])->assertSessionHasNoErrors();

        $this->assertDatabaseCount('businesses', 1);
        $this->assertDatabaseHas('businesses', ['user_id' => $owner->id, 'name' => 'Nama Baru']);
        $this->get(route('business-profile.edit'))->assertInertia(fn (Assert $page) => $page
            ->component('BusinessProfile/Edit')->where('identityLocked', false)
            ->where('business.name', 'Nama Baru'));
        $this->actingAs($other)->get(route('business-profile.edit'))->assertOk()->assertDontSee('Nama Baru');
    }

    public function test_verified_umkm_can_log_in_save_a_business_profile_and_log_out(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->umkm()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));
        $this->get(route('business-profile.edit'))->assertInertia(fn (Assert $page) => $page
            ->component('BusinessProfile/Edit')->where('business', null)->where('identityLocked', false));
        $this->put(route('business-profile.update'), [
            'name' => 'Usaha Setelah Login',
            'business_category_id' => $category,
        ])->assertRedirect(route('business-profile.edit'))->assertSessionHasNoErrors();
        $this->assertDatabaseHas('businesses', ['user_id' => $user->id, 'name' => 'Usaha Setelah Login']);

        $this->post(route('logout'))->assertRedirect('/');
        $this->get(route('business-profile.edit'))->assertRedirect(route('login'));
    }

    public function test_access_requires_authenticated_enabled_verified_umkm(): void
    {
        $this->get(route('business-profile.edit'))->assertRedirect(route('login'));
        $this->put(route('business-profile.update'))->assertRedirect(route('login'));
        foreach (['mitra', 'pemda', 'admin'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role])->refresh())->get(route('business-profile.edit'))->assertForbidden();
            $this->put(route('business-profile.update'))->assertForbidden();
        }
        $this->actingAs(User::factory()->create(['role' => 'umkm', 'account_status' => 'disabled']))->get(route('business-profile.edit'))->assertForbidden();
        $this->actingAs(User::factory()->unverified()->create(['role' => 'umkm'])->refresh())->get(route('business-profile.edit'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'umkm', 'must_change_password' => true])->refresh())->get(route('business-profile.edit'))->assertForbidden();
    }

    public function test_business_input_is_validated(): void
    {
        $user = User::factory()->create(['role' => 'umkm'])->refresh();
        $this->actingAs($user)->put(route('business-profile.update'), ['name' => '', 'business_category_id' => 999])->assertSessionHasErrors([
            'name' => 'Nama usaha wajib diisi.',
            'business_category_id' => 'Kategori usaha yang dipilih tidak valid.',
        ]);
        $this->assertDatabaseCount('businesses', 0);
    }

    public function test_verified_business_identity_cannot_be_changed_directly(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role' => 'umkm'])->refresh();
        $business = DB::table('businesses')->insertGetId(['user_id' => $user->id, 'business_category_id' => $category, 'name' => 'Awal', 'address' => 'Bandung', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('verification_requests')->insert(['business_id' => $business, 'submitted_data' => '{}', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);

        $this->actingAs($user)->put(route('business-profile.update'), ['name' => 'Berubah', 'business_category_id' => $category, 'address' => 'Bandung'])->assertSessionHasErrors('name');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'name' => 'Awal']);
        $this->actingAs($user)->put(route('business-profile.update'), ['name' => 'Awal', 'business_category_id' => $category, 'address' => 'Bandung', 'description' => 'Deskripsi baru'])->assertSessionHasNoErrors();
        $this->assertDatabaseHas('businesses', ['id' => $business, 'description' => 'Deskripsi baru']);
    }

    public function test_rejected_change_request_does_not_unlock_previously_verified_identity(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role' => 'umkm'])->refresh();
        $business = DB::table('businesses')->insertGetId(['user_id' => $user->id, 'business_category_id' => $category, 'name' => 'Awal', 'address' => 'Bandung', 'created_at' => now(), 'updated_at' => now()]);
        foreach (['approved', 'rejected'] as $status) {
            DB::table('verification_requests')->insert(['business_id' => $business, 'submitted_data' => '{}', 'status' => $status, 'created_at' => now(), 'updated_at' => now()]);
        }

        $this->actingAs($user)->get(route('business-profile.edit'))->assertInertia(fn (Assert $page) => $page
            ->component('BusinessProfile/Edit')->where('identityLocked', true));
        $this->actingAs(User::factory()->umkm()->create())->get(route('business-profile.edit'))
            ->assertInertia(fn (Assert $page) => $page->where('business', null)->where('identityLocked', false));

        $this->actingAs($user)->put(route('business-profile.update'), [
            'name' => 'Nama Baru', 'business_category_id' => $category, 'address' => 'Bandung',
        ])->assertSessionHasErrors('name');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'name' => 'Awal']);
    }

    public function test_verified_business_cannot_change_category_or_address(): void
    {
        $category = DB::table('business_categories')->insertGetId(['name' => 'Kuliner', 'created_at' => now(), 'updated_at' => now()]);
        $otherCategory = DB::table('business_categories')->insertGetId(['name' => 'Kriya', 'created_at' => now(), 'updated_at' => now()]);
        $user = User::factory()->create(['role' => 'umkm'])->refresh();
        $business = DB::table('businesses')->insertGetId(['user_id' => $user->id, 'business_category_id' => $category, 'name' => 'Awal', 'address' => 'Bandung', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('verification_requests')->insert(['business_id' => $business, 'submitted_data' => '{}', 'status' => 'approved', 'created_at' => now(), 'updated_at' => now()]);
        $data = ['name' => 'Awal', 'business_category_id' => $category, 'address' => 'Bandung'];

        $this->actingAs($user)->put(route('business-profile.update'), [...$data, 'business_category_id' => $otherCategory])->assertSessionHasErrors('business_category_id');
        $this->actingAs($user)->put(route('business-profile.update'), [...$data, 'address' => 'Jakarta'])->assertSessionHasErrors('address');
        $this->assertDatabaseHas('businesses', ['id' => $business, 'business_category_id' => $category, 'address' => 'Bandung']);
    }
}
