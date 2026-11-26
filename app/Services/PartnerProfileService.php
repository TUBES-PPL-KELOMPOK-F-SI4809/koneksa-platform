<?php

namespace App\Services;

use App\Models\PartnerProfile;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PartnerProfileService
{
    /**
     * Buat profil organisasi mitra baru untuk akun Mitra (1-to-1) beserta jenis kerja sama.
     *
     * @param  array<string, mixed>  $data
     */
    public function create(User $user, array $data): PartnerProfile
    {
        return DB::transaction(function () use ($user, $data): PartnerProfile {
            $lockedUser = $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();

            if ($lockedUser->partnerProfile()->exists()) {
                throw ValidationException::withMessages([
                    'organization_name' => 'Akun Mitra Industri hanya dapat memiliki satu profil organisasi.',
                ]);
            }

            $attributes = Arr::only($data, [
                'organization_name',
                'industry',
                'description',
                'address',
                'contact_phone',
                'website_url',
            ]);

            $profile = $lockedUser->partnerProfile()->create($attributes);

            if (array_key_exists('cooperation_type_ids', $data)) {
                $profile->cooperationTypes()->sync($data['cooperation_type_ids'] ?? []);
            }

            return $profile->load('cooperationTypes:id,name');
        });
    }

    /**
     * Perbarui profil organisasi mitra milik pemilik serta sinkronkan jenis kerja sama.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(PartnerProfile $partnerProfile, array $data): PartnerProfile
    {
        return DB::transaction(function () use ($partnerProfile, $data): PartnerProfile {
            $lockedProfile = PartnerProfile::query()
                ->whereKey($partnerProfile->id)
                ->lockForUpdate()
                ->firstOrFail();

            $attributes = Arr::only($data, [
                'organization_name',
                'industry',
                'description',
                'address',
                'contact_phone',
                'website_url',
            ]);

            $lockedProfile->update($attributes);

            if (array_key_exists('cooperation_type_ids', $data)) {
                $lockedProfile->cooperationTypes()->sync($data['cooperation_type_ids'] ?? []);
            }

            return $lockedProfile->load('cooperationTypes:id,name');
        });
    }
}

