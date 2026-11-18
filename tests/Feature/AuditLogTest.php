<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_log_service_records_action_with_actor_and_auditable(): void
    {
        $admin = User::factory()->admin()->create();
        $targetUser = User::factory()->umkm()->create();

        $this->actingAs($admin);

        $log = app(AuditLogService::class)->log(
            AuditLog::MODULE_SUPERADMIN,
            'user.status_toggled',
            "Superadmin menonaktifkan akun {$targetUser->name}",
            $targetUser,
            ['old_status' => 'enabled', 'new_status' => 'disabled']
        );

        $this->assertDatabaseHas('audit_logs', [
            'id' => $log->id,
            'actor_id' => $admin->id,
            'actor_role' => User::ROLE_ADMIN,
            'module' => AuditLog::MODULE_SUPERADMIN,
            'action' => 'user.status_toggled',
            'auditable_type' => $targetUser->getMorphClass(),
            'auditable_id' => $targetUser->id,
        ]);

        $this->assertSame('enabled', $log->metadata['old_status']);
        $this->assertSame('disabled', $log->metadata['new_status']);
    }

    public function test_audit_log_service_redacts_sensitive_fields_recursively(): void
    {
        $user = User::factory()->create();

        $log = app(AuditLogService::class)->log(
            AuditLog::MODULE_AUTH,
            'security.check',
            'Pemeriksaan sanitasi data sensitif',
            $user,
            [
                'email' => $user->email,
                'password' => 'PlainTextSecret123!',
                'current_password' => 'OldPassword123!',
                'remember_token' => 'secret_remember_token',
                'nested' => [
                    'token' => 'jwt_secret_token',
                    'api_key' => 'key_12345',
                    'safe_field' => 'aman_ditampilkan',
                ],
            ],
            $user
        );

        $this->assertSame('[REDACTED]', $log->metadata['password']);
        $this->assertSame('[REDACTED]', $log->metadata['current_password']);
        $this->assertSame('[REDACTED]', $log->metadata['remember_token']);
        $this->assertSame('[REDACTED]', $log->metadata['nested']['token']);
        $this->assertSame('[REDACTED]', $log->metadata['nested']['api_key']);
        $this->assertSame('aman_ditampilkan', $log->metadata['nested']['safe_field']);

        // Pastikan plaintext secret tidak pernah tersimpan di kolom JSON database
        $rawJson = json_encode($log->metadata);
        $this->assertStringNotContainsString('PlainTextSecret123!', (string) $rawJson);
        $this->assertStringNotContainsString('jwt_secret_token', (string) $rawJson);
    }

    public function test_registration_creates_audit_log_without_exposing_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'UMKM Baru',
            'email' => 'umkmbaru@koneksa.id',
            'role' => User::ROLE_UMKM,
            'password' => 'Rahasia12345!',
            'password_confirmation' => 'Rahasia12345!',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));

        $log = AuditLog::where('action', 'user.registered')->first();
        $this->assertNotNull($log);
        $this->assertSame(AuditLog::MODULE_AUTH, $log->module);
        $this->assertSame(User::ROLE_UMKM, $log->actor_role);
        $this->assertStringNotContainsString('Rahasia12345!', json_encode($log->toArray()));
    }

    public function test_login_and_logout_create_audit_logs(): void
    {
        $user = User::factory()->umkm()->create();

        // Login
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect('/dashboard');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $user->id,
            'module' => AuditLog::MODULE_AUTH,
            'action' => 'auth.login',
        ]);

        // Logout
        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $user->id,
            'module' => AuditLog::MODULE_AUTH,
            'action' => 'auth.logout',
        ]);
    }

    public function test_failed_login_does_not_create_success_audit_log(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'salah-password',
        ]);

        $this->assertDatabaseMissing('audit_logs', [
            'actor_id' => $user->id,
            'action' => 'auth.login',
        ]);
    }

    public function test_password_update_creates_audit_log_only_on_success(): void
    {
        $user = User::factory()->create();

        // Percobaan gagal (password lama salah)
        $this->actingAs($user)->put('/password', [
            'current_password' => 'wrong-password',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $this->assertDatabaseMissing('audit_logs', [
            'actor_id' => $user->id,
            'action' => 'user.password_changed',
        ]);

        // Percobaan berhasil
        $this->actingAs($user)->put('/password', [
            'current_password' => 'password',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $user->id,
            'module' => AuditLog::MODULE_AUTH,
            'action' => 'user.password_changed',
        ]);
    }

    public function test_superadmin_status_toggle_creates_audit_log(): void
    {
        $admin = User::factory()->admin()->create();
        $umkm = User::factory()->umkm()->create(['account_status' => User::STATUS_ENABLED]);

        $this->actingAs($admin)
            ->patch(route('admin.users.status', $umkm), [
                'account_status' => User::STATUS_DISABLED,
            ])
            ->assertOk();

        $this->assertDatabaseHas('audit_logs', [
            'actor_id' => $admin->id,
            'actor_role' => User::ROLE_ADMIN,
            'module' => AuditLog::MODULE_SUPERADMIN,
            'action' => 'user.status_toggled',
            'auditable_id' => $umkm->id,
        ]);
    }

    public function test_only_superadmin_can_access_audit_trail_page(): void
    {
        // Guest -> redirect ke login
        $this->get(route('admin.audit_logs.index'))->assertRedirect(route('login'));

        // UMKM, Mitra, Pemda -> 403 Forbidden
        $umkm = User::factory()->umkm()->create();
        $mitra = User::factory()->mitra()->create();
        $pemda = User::factory()->pemda()->create();

        $this->actingAs($umkm)->get(route('admin.audit_logs.index'))->assertForbidden();
        $this->actingAs($mitra)->get(route('admin.audit_logs.index'))->assertForbidden();
        $this->actingAs($pemda)->get(route('admin.audit_logs.index'))->assertForbidden();

        // Superadmin -> 200 OK
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.audit_logs.index'))->assertOk();
    }

    public function test_superadmin_can_filter_audit_logs(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Admin Utama']);
        $umkm = User::factory()->umkm()->create(['name' => 'Budi Santoso']);

        app(AuditLogService::class)->log(
            AuditLog::MODULE_AUTH,
            'auth.login',
            'Budi Santoso masuk ke sistem',
            $umkm,
            [],
            $umkm
        );

        app(AuditLogService::class)->log(
            AuditLog::MODULE_SUPERADMIN,
            'user.status_toggled',
            'Admin Utama mengubah status akun',
            $umkm,
            [],
            $admin
        );

        // Filter berdasarkan module = superadmin
        $response = $this->actingAs($admin)
            ->get(route('admin.audit_logs.index', ['module' => AuditLog::MODULE_SUPERADMIN]));

        $response->assertOk();
        $logs = $response->viewData('page')['props']['logs']['data'];
        $this->assertCount(1, $logs);
        $this->assertSame('user.status_toggled', $logs[0]['action']);

        // Filter berdasarkan search = Budi Santoso
        $responseSearch = $this->actingAs($admin)
            ->get(route('admin.audit_logs.index', ['search' => 'Budi Santoso']));

        $responseSearch->assertOk();
        $searchLogs = $responseSearch->viewData('page')['props']['logs']['data'];
        $this->assertCount(1, $searchLogs);
        $this->assertSame('auth.login', $searchLogs[0]['action']);
    }
}
