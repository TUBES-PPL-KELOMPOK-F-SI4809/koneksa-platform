<?php

namespace App\Http\Requests\PartnerProfile;

use App\Models\PartnerProfile;

class UpdatePartnerProfileRequest extends PartnerProfileRequest
{
    public function authorize(): bool
    {
        $partnerProfile = $this->route('partnerProfile');

        return $partnerProfile instanceof PartnerProfile
            && ($this->user()?->can('update', $partnerProfile) ?? false);
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->sharedRules(isCreate: false);
    }
}
