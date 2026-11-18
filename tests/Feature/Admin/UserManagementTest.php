<?php

namespace Tests\Feature\Admin;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_superadmin_can_view_user_management_list(): void
    {
        // Guest -> redirect ke login
        $this->get(route('admin.users.index'))->assertRedirect(route('login'));

        // UMKM, Mitra, Pemda -> 403 Forbidden
        $umkm = User::factory()->umkm()->create();
        $mitra = User::factory()->mitra()->create();
        $pemda = User::factory()->pemda()->create();

        $this->actingAs($umkm)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($mitra)->get(route('admin.users.index'))->assertForbidden();
        $this->actingAs($pemda)->get(route('admin.users.index'))->assertForbidden();

        // Superadmin -> 200 OK
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.users.index'))->assertOk();
    }

    public function test_superadmin_can_filter_users_by_status_role_and_search(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Superadmin Sistem']);
        $umkmEnabled = User::factory()->umkm()->create([
            'name' => 'Warung Kopi Nusantara',
            'account_status' => User::STATUS_ENABLED,
        ]);
        $mitraDisabled = User::factory()->mitra()->disabled()->create([
            'name' => 'PT Digital Solusi',
        ]);

        // Filter berdasarkan status = disabled
        $resStatus = $this->actingAs($admin)
            ->getJson(route('admin.users.index', ['account_status' => User::STATUS_DISABLED]));

        $resStatus->assertOk();
        $disabledUsers = $resStatus->json('users');
        $this->assertCount(1, $disabledUsers);
        $this->assertSame($mitraDisabled->id, $disabledUsers[0]['id']);

        // Filter berdasarkan role = umkm & search = Kopi
        $resSearch = $this->actingAs($admin)
            ->getJson(route('admin.users.index', [
                'role' => User::ROLE_UMKM,
                'search' => 'Kopi',
            ]));

        $resSearch->assertOk();
        $matchedUsers = $resSearch->json('users');
        $this->assertCount(1, $matchedUsers);
        $this->assertSame($umkmEnabled->id, $matchedUsers[0]['id']);
    }

    public function test_superadmin_can_create_pemda_user_with_mandatory_password_change_and_audit_log(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post(route('admin.users.pemda.store'), [
                'name' => 'Dinas Koperasi Kota Bandung',
                'email' => 'dinkop.bandung@pemda.go.id',
                'phone' => '081234567890',
                'password' => 'PemdaSementara123!',
                'password_confirmation' => 'PemdaSementara123!',
            ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('status', 'pemda-user-created');

        $pemdaUser = User::where('email', 'dinkop.bandung@pemda.go.id')->first();
        $this->assertNotNull($pemdaUser);
        $this->assertSame(User::ROLE_PEMDA, $pemdaUser->role);
        $this->assertSame(User::STATUS_ENABLED, $pemdaUser->account_status);
        $this->assertTrue($pemdaUser->must_change_password);
        $this->assertTrue(Hash::check('PemdaSementara123!', $pemdaUser->password));

        // Pastikan tercatat di Audit Trail tanpa membocorkan plaintext password
        $audit = AuditLog::where('action', 'user.pemda_created')->first();
        $this->assertNotNull($audit);
        $this->assertSame($admin->id, $audit->actor_id);
        $this->assertSame($pemdaUser->id, $audit->auditable_id);
        $this->assertStringNotContainsString('PemdaSementara123!', json_encode($audit->toArray()));
    }

    public function test_non_admin_cannot_create_pemda_user(): void
    {
        $umkm = User::factory()->umkm()->create();
        $pemda = User::factory()->pemda()->create();

        $payload = [
            'name' => 'Pemda Ilegal',
            'email' => 'ilegal@pemda.go.id',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ];

        $this->actingAs($umkm)->post(route('admin.users.pemda.store'), $payload)->assertForbidden();
        $this->actingAs($pemda)->post(route('admin.users.pemda.store'), $payload)->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'ilegal@pemda.go.id']);
    }

    public function test_pemda_creation_validates_unique_email_and_phone_format(): void
    {
        $admin = User::factory()->admin()->create();
        $existing = User::factory()->create(['email' => 'terdaftar@koneksa.id']);

        $response = $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->post(route('admin.users.pemda.store'), [
                'name' => 'Dinas Duplikat',
                'email' => $existing->email,
                'phone' => 'invalid-phone',
                'password' => 'Password123!',
                'password_confirmation' => 'Password123!',
            ]);

        $response->assertSessionHasErrors(['email', 'phone']);
    }

    public function test_superadmin_can_enable_and_disable_other_user_account_with_audit_log(): void
    {
        $admin = User::factory()->admin()->create();
        $umkm = User::factory()->umkm()->create(['account_status' => User::STATUS_ENABLED]);

        // Nonaktifkan akun UMKM
        $this->actingAs($admin)
            ->patch(route('admin.users.status', $umkm), [
                'account_status' => User::STATUS_DISABLED,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(User::STATUS_DISABLED, $umkm->refresh()->account_status);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'user.status_toggled',
            'auditable_id' => $umkm->id,
        ]);

        // Aktifkan kembali akun UMKM
        $this->actingAs($admin)
            ->patch(route('admin.users.status', $umkm), [
                'account_status' => User::STATUS_ENABLED,
            ])
            ->assertRedirect(route('admin.users.index'));

        $this->assertSame(User::STATUS_ENABLED, $umkm->refresh()->account_status);
    }

    public function test_superadmin_cannot_disable_or_delete_own_account(): void
    {
        $admin = User::factory()->admin()->create(['account_status' => User::STATUS_ENABLED]);

        // Coba nonaktifkan diri sendiri -> 403 Forbidden
        $this->actingAs($admin)
            ->patch(route('admin.users.status', $admin), [
                'account_status' => User::STATUS_DISABLED,
            ])
            ->assertForbidden();

        $this->assertSame(User::STATUS_ENABLED, $admin->refresh()->account_status);

        // Coba hapus diri sendiri -> 403 Forbidden
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertForbidden();

        $this->assertNotNull(User::find($admin->id));
    }

    public function test_superadmin_can_soft_delete_other_user_account_with_audit_log(): void
    {
        $admin = User::factory()->admin()->create();
        $mitra = User::factory()->mitra()->create();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $mitra))
            ->assertRedirect(route('admin.users.index'));

        $this->assertSoftDeleted($mitra);
        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'action' => 'user.deleted_by_admin',
            'auditable_id' => $mitra->id,
        ]);
    }
}
