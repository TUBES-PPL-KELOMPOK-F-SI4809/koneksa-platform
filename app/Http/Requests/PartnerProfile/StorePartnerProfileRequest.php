<?php

namespace App\Http\Requests\PartnerProfile;

use App\Models\PartnerProfile;

class StorePartnerProfileRequest extends PartnerProfileRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PartnerProfile::class) ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return $this->sharedRules(isCreate: true);
    }
}

