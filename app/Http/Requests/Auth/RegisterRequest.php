<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Izinkan request ini dijalankan publik.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi registrasi publik.
     * Hanya role UMKM dan Mitra Industri yang diizinkan mendaftar secara publik (BR-01, FR-01.1).
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'role' => ['required', 'string', Rule::in(User::PUBLIC_ROLES)],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * Pesan kustom untuk validasi registrasi.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'role.in' => 'Pendaftaran publik hanya diizinkan untuk peran Pelaku UMKM atau Mitra Industri.',
        ];
    }
}
