<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StorePemdaUserRequest extends FormRequest
{
    /**
     * Hanya Superadmin yang memiliki otorisasi membuat akun Pemda (FR-03.2 / KSA-40).
     */
    public function authorize(): bool
    {
        return $this->user()?->can('createPemda', User::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(\+?[0-9]{8,20})$/',
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Pesan kesalahan kustom dalam Bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
            'phone.regex' => 'Format nomor telepon tidak valid (gunakan 8-20 digit angka, opsional awalan +).',
        ];
    }
}
