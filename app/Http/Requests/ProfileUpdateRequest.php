<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan melakukan request ini.
     * Hanya pemilik akun terautentikasi yang diizinkan (BR-01, FR-02.1).
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Aturan validasi profil dasar akun.
     * Hanya field dasar (name, email, phone) yang boleh diperbarui.
     * Role, account_status, dan field privileged lain diabaikan dan tidak dimasukkan ke rules.
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
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone' => [
                'nullable',
                'string',
                'max:30',
                'regex:/^(\+?[0-9]{8,20})$/',
            ],
        ];
    }

    /**
     * Pesan kustom untuk validasi profil akun.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Format nomor telepon tidak valid. Gunakan format angka nasional atau internasional (contoh: 08123456789 atau +628123456789).',
        ];
    }
}
