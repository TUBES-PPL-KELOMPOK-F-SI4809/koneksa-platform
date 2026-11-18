<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BusinessProfileTest extends TestCase
{
    use RefreshDatabase;

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
        $this->actingAs($other)->get(route('business-profile.edit'))->assertOk()->assertDontSee('Nama Baru');
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
