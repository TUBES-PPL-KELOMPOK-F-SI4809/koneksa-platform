<?php

namespace App\Http\Requests\PartnerProfile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

abstract class PartnerProfileRequest extends FormRequest
{
    /**
     * Aturan validasi bersama untuk profil organisasi mitra (FR-16.1, FR-16.2).
     *
     * @return array<string, array<int, mixed>>
     */
    protected function sharedRules(bool $isCreate): array
    {
        $requiredRule = $isCreate ? ['required'] : ['sometimes', 'required'];

        return [
            'organization_name' => ['required', 'string', 'max:150'],
            'industry' => [...$requiredRule, 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:10000'],
            'address' => ['nullable', 'string', 'max:10000'],
            'contact_phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{8,30}$/'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'cooperation_type_ids' => [...$requiredRule, 'array', 'min:1'],
            'cooperation_type_ids.*' => ['required', 'integer', 'distinct', Rule::exists('cooperation_types', 'id')],
        ];
    }

    /**
     * Pesan kesalahan validasi berbahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max.string' => ':attribute tidak boleh lebih dari :max karakter.',
            'array' => ':attribute harus berupa daftar pilihan.',
            'min.array' => 'Pilih minimal :min :attribute.',
            'integer' => 'Pilihan :attribute tidak valid.',
            'distinct' => 'Pilihan :attribute tidak boleh duplikat.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'url' => ':attribute harus berupa URL yang valid (contoh: https://perusahaan.co.id).',
            'contact_phone.regex' => 'Format telepon organisasi tidak valid. Gunakan angka 8–30 karakter (contoh: 081198765001 atau +6281198765001).',
        ];
    }

    /**
     * Nama atribut ramah pengguna untuk pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'organization_name' => 'Nama organisasi',
            'industry' => 'Kategori entitas bisnis',
            'description' => 'Deskripsi organisasi dan dukungan bisnis',
            'address' => 'Alamat organisasi',
            'contact_phone' => 'Telepon organisasi',
            'website_url' => 'Situs web organisasi',
            'cooperation_type_ids' => 'jenis kerja sama dan dukungan bisnis',
            'cooperation_type_ids.*' => 'jenis kerja sama',
        ];
    }
}

