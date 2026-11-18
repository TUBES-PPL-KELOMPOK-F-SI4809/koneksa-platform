<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserStatusRequest extends FormRequest
{
    /**
     * Hanya Superadmin yang dapat mengubah status akun, dan dilarang menonaktifkan diri sendiri (FR-03.1 / KSA-39).
     */
    public function authorize(): bool
    {
        $targetUser = $this->route('user');

        if (! $targetUser instanceof User) {
            return false;
        }

        return $this->user()?->can('manageStatus', $targetUser) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_status' => [
                'required',
                'string',
                Rule::in(User::ACCOUNT_STATUSES),
            ],
        ];
    }
}
