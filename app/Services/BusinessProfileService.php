<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BusinessProfileService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function save(User $user, array $data): void
    {
        DB::transaction(function () use ($user, $data): void {
            $user = $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
            $business = $user->business()->lockForUpdate()->first();

            if ($business && DB::table('verification_requests')
                ->where('business_id', $business->id)->where('status', 'approved')->exists()) {
                foreach (['name', 'business_category_id', 'address'] as $field) {
                    if ((string) ($business->{$field} ?? '') !== (string) ($data[$field] ?? '')) {
                        throw ValidationException::withMessages([$field => 'Identitas usaha terverifikasi tidak dapat diubah langsung.']);
                    }
                }
            }

            $user->business()->updateOrCreate([], $data);
        });
    }
}
