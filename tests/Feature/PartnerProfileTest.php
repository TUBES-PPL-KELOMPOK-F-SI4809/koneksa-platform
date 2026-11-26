<?php

namespace Tests\Feature;

use App\Models\CooperationType;
use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PartnerProfileTest extends TestCase
{
    use RefreshDatabase;

    private CooperationType $capitalType;

    private CooperationType $mentoringType;

    private CooperationType $digitalType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->capitalType = CooperationType::create(['name' => 'Permodalan & Pembiayaan Usaha']);
        $this->mentoringType = CooperationType::create(['name' => 'Pelatihan, Workshop & Mentoring']);
        $this->digitalType = CooperationType::create(['name' => 'Digitalisasi & Adopsi Teknologi']);
    }

    public function test_guest_and_non_mitra_roles_are_rejected(): void
    {
        $owner = User::factory()->mitra()->create();
        $profile = PartnerProfile::factory()->create(['user_id' => $owner->id]);

        // Guest redirects to login
        $this->get(route('mitra.profile.edit'))->assertRedirect(route('login'));
        $this->post(route('mitra.profile.store'), [])->assertRedirect(route('login'));
        $this->put(route('mitra.profile.update', $profile), [])->assertRedirect(route('login'));
        $this->delete(route('mitra.profile.destroy', $profile))->assertRedirect(route('login'));

        // Non-mitra roles are forbidden (BR-01)
        foreach (['umkm', 'pemda', 'admin'] as $role) {
            $nonMitra = User::factory()->create(['role' => $role])->refresh();

            $this->actingAs($nonMitra)->get(route('mitra.profile.edit'))->assertForbidden();
            $this->actingAs($nonMitra)->post(route('mitra.profile.store'), [
                'organization_name' => 'Organisasi Tidak Sah',
                'industry' => 'Perusahaan / Korporasi',
                'cooperation_type_ids' => [$this->capitalType->id],
            ])->assertForbidden();
            $this->actingAs($nonMitra)->put(route('mitra.profile.update', $profile), [
                'organization_name' => 'Diubah Non-Mitra',
            ])->assertForbidden();
            $this->actingAs($nonMitra)->delete(route('mitra.profile.destroy', $profile))->assertForbidden();
        }
    }

    public function test_unverified_disabled_or_password_change_required_mitra_cannot_manage_profile(): void
    {
        $unverified = User::factory()->mitra()->unverified()->create()->refresh();
        $mustChange = User::factory()->mitra()->mustChangePassword()->create()->refresh();
        $disabled = User::factory()->mitra()->disabled()->create()->refresh();

        $this->actingAs($unverified)->get(route('mitra.profile.edit'))->assertForbidden();
        $this->actingAs($mustChange)->get(route('mitra.profile.edit'))->assertForbidden();
        $this->actingAs($disabled)->get(route('mitra.profile.edit'))->assertRedirect(route('login'));
        $this->actingAs($disabled)->getJson(route('mitra.profile.edit'))->assertForbidden();
    }

    public function test_mitra_can_view_empty_state_create_profile_and_sync_cooperation_types(): void
    {
        $mitra = User::factory()->mitra()->create();
        $otherUser = User::factory()->mitra()->create();

        $this->actingAs($mitra)
            ->get(route('mitra.profile.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('PartnerProfile/Edit')
                ->where('partnerProfile', null)
                ->has('cooperationTypes', 3)
                ->has('entityCategories'));

        $response = $this->actingAs($mitra)->post(route('mitra.profile.store'), [
            'user_id' => $otherUser->id, // Percobaan forgery user_id harus diabaikan
            'organization_name' => 'PT Akselerasi Digital Bandung',
            'industry' => 'Teknologi Informasi & Telekomunikasi',
            'description' => 'Menyediakan sistem kasir cloud dan pendampingan transformasi digital UMKM.',
            'address' => 'Jl. Dipatiukur No. 45, Kota Bandung',
            'contact_phone' => '081122334455',
            'website_url' => 'https://akselerasidigital.id',
            'cooperation_type_ids' => [$this->mentoringType->id, $this->digitalType->id],
        ]);

        $response->assertRedirect(route('mitra.profile.edit'))->assertSessionHasNoErrors();

        $profile = PartnerProfile::query()->where('user_id', $mitra->id)->firstOrFail();
        $this->assertSame('PT Akselerasi Digital Bandung', $profile->organization_name);
        $this->assertSame('Teknologi Informasi & Telekomunikasi', $profile->industry);
        $this->assertNull(PartnerProfile::query()->where('user_id', $otherUser->id)->first());

        $this->assertEqualsCanonicalizing(
            [$this->mentoringType->id, $this->digitalType->id],
            $profile->cooperationTypes()->pluck('cooperation_types.id')->all()
        );

        $this->actingAs($mitra)
            ->get(route('mitra.profile.edit'))
            ->assertInertia(fn (Assert $page) => $page
                ->component('PartnerProfile/Edit')
                ->where('partnerProfile.id', $profile->id)
                ->where('partnerProfile.organization_name', 'PT Akselerasi Digital Bandung')
                ->where('partnerProfile.industry', 'Teknologi Informasi & Telekomunikasi')
                ->where('partnerProfile.cooperation_type_ids', [$this->mentoringType->id, $this->digitalType->id]));
    }

    public function test_mitra_cannot_create_second_partner_profile_and_database_enforces_uniqueness(): void
    {
        $mitra = User::factory()->mitra()->create();
        PartnerProfile::factory()->create(['user_id' => $mitra->id]);

        $this->actingAs($mitra)->post(route('mitra.profile.store'), [
            'organization_name' => 'Profil Kedua',
            'industry' => 'Perusahaan / Korporasi',
            'cooperation_type_ids' => [$this->capitalType->id],
        ])->assertForbidden();

        $this->assertDatabaseCount('partner_profiles', 1);

        $this->expectException(QueryException::class);
        DB::table('partner_profiles')->insert([
            'user_id' => $mitra->id,
            'organization_name' => 'Duplikat DB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_owner_can_update_and_delete_own_profile_while_other_mitra_is_forbidden(): void
    {
        $owner = User::factory()->mitra()->create();
        $stranger = User::factory()->mitra()->create();

        $profile = PartnerProfile::factory()->create([
            'user_id' => $owner->id,
            'organization_name' => 'PT Mitra Awal',
            'industry' => 'Perusahaan / Korporasi',
        ]);
        $profile->cooperationTypes()->sync([$this->capitalType->id, $this->mentoringType->id]);

        // Mitra lain tidak boleh melihat form dengan data pemilik, mengubah, atau menghapus profil pemilik
        $this->actingAs($stranger)
            ->get(route('mitra.profile.edit'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->where('partnerProfile', null));

        $this->actingAs($stranger)
            ->put(route('mitra.profile.update', $profile), [
                'organization_name' => 'PT Dibajak',
                'industry' => 'Investor / Modal Ventura',
                'cooperation_type_ids' => [$this->digitalType->id],
            ])
            ->assertForbidden();

        $this->actingAs($stranger)
            ->delete(route('mitra.profile.destroy', $profile))
            ->assertForbidden();

        $this->assertDatabaseHas('partner_profiles', [
            'id' => $profile->id,
            'organization_name' => 'PT Mitra Awal',
        ]);

        // Pemilik dapat memperbarui profil dan mengganti jenis kerja sama
        $this->actingAs($owner)
            ->put(route('mitra.profile.update', $profile), [
                'organization_name' => 'PT Mitra Terbarui',
                'industry' => 'Perbankan & Jasa Keuangan',
                'description' => 'Program pembiayaan mikro dan literasi keuangan UMKM.',
                'address' => 'Jl. Asia Afrika No. 10, Bandung',
                'contact_phone' => '+6281199887766',
                'website_url' => 'https://mitraterbarui.co.id',
                'cooperation_type_ids' => [$this->digitalType->id],
            ])
            ->assertRedirect(route('mitra.profile.edit'))
            ->assertSessionHasNoErrors();

        $profile->refresh();
        $this->assertSame('PT Mitra Terbarui', $profile->organization_name);
        $this->assertSame('Perbankan & Jasa Keuangan', $profile->industry);
        $this->assertSame([$this->digitalType->id], $profile->cooperationTypes()->pluck('cooperation_types.id')->all());

        // Endpoint show mengembalikan detail profil beserta relasi cooperation_types
        $this->actingAs($owner)
            ->getJson(route('mitra.profile.show', $profile))
            ->assertOk()
            ->assertJsonPath('organization_name', 'PT Mitra Terbarui')
            ->assertJsonCount(1, 'cooperation_types');

        // Pemilik dapat menghapus profil miliknya jika tidak terkunci relasi
        $this->actingAs($owner)
            ->delete(route('mitra.profile.destroy', $profile))
            ->assertRedirect(route('mitra.profile.edit'));

        $this->assertDatabaseMissing('partner_profiles', ['id' => $profile->id]);
    }

    public function test_delete_is_blocked_gracefully_when_referenced_by_digital_offering(): void
    {
        $owner = User::factory()->mitra()->create();
        $profile = PartnerProfile::factory()->create(['user_id' => $owner->id]);

        DB::table('digital_offerings')->insert([
            'partner_profile_id' => $profile->id,
            'title' => 'Solusi Kasir Digital',
            'description' => 'Layanan POS untuk UMKM',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->actingAs($owner)
            ->deleteJson(route('mitra.profile.destroy', $profile))
            ->assertStatus(409);

        $this->assertDatabaseHas('partner_profiles', ['id' => $profile->id]);
    }

    public function test_profile_input_is_validated_on_create_and_update(): void
    {
        $mitra = User::factory()->mitra()->create();

        // Validasi saat create: field wajib kosong, URL/telepon tidak valid, cooperation_type_ids tidak valid
        $this->actingAs($mitra)->post(route('mitra.profile.store'), [
            'organization_name' => '',
            'industry' => '',
            'contact_phone' => 'bukan-telepon',
            'website_url' => 'bukan-url',
            'cooperation_type_ids' => [],
        ])->assertSessionHasErrors([
            'organization_name',
            'industry',
            'contact_phone',
            'website_url',
            'cooperation_type_ids',
        ]);

        $this->actingAs($mitra)->post(route('mitra.profile.store'), [
            'organization_name' => 'PT Valid',
            'industry' => 'Perusahaan / Korporasi',
            'cooperation_type_ids' => [999999],
        ])->assertSessionHasErrors(['cooperation_type_ids.0']);

        $this->assertDatabaseCount('partner_profiles', 0);

        // Validasi saat update: gagal validasi tidak mengubah data lama
        $profile = PartnerProfile::factory()->create([
            'user_id' => $mitra->id,
            'organization_name' => 'PT Asli Tetap',
            'industry' => 'Perusahaan / Korporasi',
        ]);
        $profile->cooperationTypes()->sync([$this->capitalType->id]);

        $this->actingAs($mitra)->put(route('mitra.profile.update', $profile), [
            'organization_name' => '',
            'industry' => '',
            'website_url' => 'invalid-url',
            'cooperation_type_ids' => [],
        ])->assertSessionHasErrors([
            'organization_name',
            'industry',
            'website_url',
            'cooperation_type_ids',
        ]);

        $this->assertDatabaseHas('partner_profiles', [
            'id' => $profile->id,
            'organization_name' => 'PT Asli Tetap',
            'industry' => 'Perusahaan / Korporasi',
        ]);
        $this->assertSame([$this->capitalType->id], $profile->cooperationTypes()->pluck('cooperation_types.id')->all());
    }
}
