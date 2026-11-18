<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserDataIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_table_has_expected_schema_and_columns(): void
    {
        $expectedColumns = [
            'id',
            'name',
            'email',
            'role',
            'account_status',
            'password',
            'phone',
            'avatar_path',
            'email_verified_at',
            'must_change_password',
            'current_session_id',
            'remember_token',
            'created_at',
            'updated_at',
            'deleted_at',
        ];

        foreach ($expectedColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('users', $column),
                "Column '{$column}' is missing from 'users' table."
            );
        }
    }

    public function test_default_user_role_and_account_status(): void
    {
        $user = User::create([
            'name' => 'Default User',
            'email' => 'default@koneksa.id',
            'password' => 'secret123',
        ]);

        $this->assertEquals(User::ROLE_UMKM, $user->role);
        $this->assertEquals(User::STATUS_ENABLED, $user->account_status);
        $this->assertFalse($user->must_change_password);
        $this->assertTrue($user->isUmkm());
        $this->assertTrue($user->isEnabled());
        $this->assertFalse($user->isDisabled());

        // Pastikan nilai di database juga sama persis
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'umkm',
            'account_status' => 'enabled',
            'must_change_password' => false,
        ]);
    }

    public function test_user_soft_deletes_preserves_historical_record(): void
    {
        $user = User::factory()->create([
            'name' => 'Soft Delete Test',
            'email' => 'softdelete@koneksa.id',
        ]);

        $userId = $user->id;
        $user->delete();

        // Rekord tetap ada di database dengan deleted_at terisi
        $this->assertDatabaseHas('users', [
            'id' => $userId,
        ]);
        $this->assertNotNull($user->fresh()?->deleted_at);

        // Terfilter dari query default
        $this->assertNull(User::find($userId));

        // Dapat diakses dengan withTrashed
        $this->assertNotNull(User::withTrashed()->find($userId));

        // Restore berfungsi
        $user->restore();
        $this->assertNotNull(User::find($userId));
        $this->assertNull($user->fresh()->deleted_at);
    }

    public function test_user_has_one_business_relationship_and_foreign_key_constraint(): void
    {
        $category = BusinessCategory::create([
            'name' => 'Kuliner',
        ]);

        $user = User::factory()->umkm()->create();

        $business = Business::create([
            'user_id' => $user->id,
            'business_category_id' => $category->id,
            'name' => 'Kopi Aroma Bandung',
            'description' => 'Usaha kopi legendaris',
            'address' => 'Jl. Braga No. 10',
            'district' => 'Sumur Bandung',
            'subdistrict' => 'Braga',
            'contact_phone' => '081234567890',
        ]);

        // Uji relasi User -> Business dan Business -> User
        $this->assertInstanceOf(Business::class, $user->business);
        $this->assertEquals($business->id, $user->business->id);
        $this->assertEquals($user->id, $business->user->id);

        // Relasi 1-to-1: user_id bersifat unique, duplikasi harus ditolak
        $this->expectException(QueryException::class);
        Business::create([
            'user_id' => $user->id,
            'business_category_id' => $category->id,
            'name' => 'Usaha Kedua (Tidak Boleh)',
        ]);
    }

    public function test_user_force_delete_is_restricted_when_business_exists(): void
    {
        $category = BusinessCategory::create([
            'name' => 'Teknologi',
        ]);

        $user = User::factory()->umkm()->create();

        Business::create([
            'user_id' => $user->id,
            'business_category_id' => $category->id,
            'name' => 'Solusi Digital',
        ]);

        // Restrict on delete mencegah forceDelete jika child business masih ada
        $this->expectException(QueryException::class);
        $user->forceDelete();
    }

    public function test_user_has_one_partner_profile_relationship_and_foreign_key_constraint(): void
    {
        $user = User::factory()->mitra()->create();

        $partner = PartnerProfile::create([
            'user_id' => $user->id,
            'organization_name' => 'PT Mitra Industri Jaya',
            'industry' => 'Manufaktur & FMCG',
            'description' => 'Penyedia bahan baku industri',
            'contact_phone' => '081298765432',
        ]);

        // Uji relasi User -> PartnerProfile dan PartnerProfile -> User
        $this->assertInstanceOf(PartnerProfile::class, $user->partnerProfile);
        $this->assertEquals($partner->id, $user->partnerProfile->id);
        $this->assertEquals($user->id, $partner->user->id);

        // Relasi 1-to-1: user_id bersifat unique
        $this->expectException(QueryException::class);
        PartnerProfile::create([
            'user_id' => $user->id,
            'organization_name' => 'Profil Mitra Kedua (Tidak Boleh)',
        ]);
    }

    public function test_user_force_delete_is_restricted_when_partner_profile_exists(): void
    {
        $user = User::factory()->mitra()->create();

        PartnerProfile::create([
            'user_id' => $user->id,
            'organization_name' => 'PT Ritel Bersama',
        ]);

        // Restrict on delete mencegah forceDelete jika child partner_profile masih ada
        $this->expectException(QueryException::class);
        $user->forceDelete();
    }

    public function test_email_uniqueness_constraint(): void
    {
        User::factory()->create(['email' => 'unique@koneksa.id']);

        $this->expectException(QueryException::class);
        User::factory()->create(['email' => 'unique@koneksa.id']);
    }

    public function test_current_session_id_is_hidden_from_serialization(): void
    {
        $user = User::factory()->create([
            'current_session_id' => 'sess_12345abcdef',
        ]);

        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
        $this->assertArrayNotHasKey('current_session_id', $array);
    }

    public function test_role_helper_methods_and_scopes(): void
    {
        $umkm = User::factory()->umkm()->create();
        $mitra = User::factory()->mitra()->create();
        $pemda = User::factory()->pemda()->create();
        $admin = User::factory()->admin()->create();
        $disabled = User::factory()->disabled()->create(['role' => User::ROLE_ADMIN]);

        // Helper methods
        $this->assertTrue($umkm->isUmkm());
        $this->assertFalse($umkm->isMitra());

        $this->assertTrue($mitra->isMitra());
        $this->assertTrue($pemda->isPemda());
        $this->assertTrue($admin->isAdmin());

        $this->assertTrue($umkm->hasRole('umkm'));
        $this->assertTrue($umkm->hasRole(['umkm', 'mitra']));
        $this->assertFalse($umkm->hasRole(['mitra', 'pemda']));

        $this->assertTrue($umkm->isEnabled());
        $this->assertTrue($disabled->isDisabled());

        // Scope Active
        $activeUsers = User::active()->pluck('id');
        $this->assertTrue($activeUsers->contains($umkm->id));
        $this->assertFalse($activeUsers->contains($disabled->id));

        // Scope Role
        $this->assertEquals(2, User::role(User::ROLE_ADMIN)->count());
        $this->assertEquals(1, User::role(User::ROLE_PEMDA)->count());
        $this->assertEquals(2, User::role([User::ROLE_UMKM, User::ROLE_MITRA])->count());
    }

    public function test_user_factory_states(): void
    {
        $umkm = User::factory()->umkm()->make();
        $mitra = User::factory()->mitra()->make();
        $pemda = User::factory()->pemda()->make();
        $admin = User::factory()->admin()->make();
        $disabled = User::factory()->disabled()->make();
        $mustChange = User::factory()->mustChangePassword()->make();

        $this->assertEquals(User::ROLE_UMKM, $umkm->role);
        $this->assertEquals(User::ROLE_MITRA, $mitra->role);
        $this->assertEquals(User::ROLE_PEMDA, $pemda->role);
        $this->assertEquals(User::ROLE_ADMIN, $admin->role);
        $this->assertEquals(User::STATUS_DISABLED, $disabled->account_status);
        $this->assertTrue($mustChange->must_change_password);
    }
}
