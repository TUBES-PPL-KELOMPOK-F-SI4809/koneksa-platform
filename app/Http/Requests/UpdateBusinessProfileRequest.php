<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-business-profile');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'business_category_id' => ['required', 'integer', Rule::exists('business_categories', 'id')],
            'description' => ['nullable', 'string', 'max:10000'],
            'address' => ['nullable', 'string', 'max:10000'],
            'district' => ['nullable', 'string', 'max:100'],
            'subdistrict' => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'business_needs' => ['nullable', 'string', 'max:10000'],
            'is_published' => ['sometimes', 'required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max.string' => ':attribute tidak boleh lebih dari :max karakter.',
            'integer' => ':attribute tidak valid.',
            'exists' => ':attribute yang dipilih tidak valid.',
            'boolean' => ':attribute harus berupa pilihan publik atau draft.',
            'business_needs.max' => 'Kebutuhan pengembangan usaha tidak boleh lebih dari :max karakter.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Nama usaha',
            'business_category_id' => 'Kategori usaha',
            'description' => 'Deskripsi usaha',
            'address' => 'Alamat',
            'district' => 'Kecamatan',
            'subdistrict' => 'Kelurahan',
            'contact_phone' => 'Telepon usaha',
            'business_needs' => 'Kebutuhan pengembangan usaha',
            'is_published' => 'Status publikasi',
        ];
    }
}
