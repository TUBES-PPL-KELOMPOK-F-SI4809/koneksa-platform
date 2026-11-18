<?php

namespace App\Http\Requests\Inventory;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('restock', $this->route('variant'));
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'quantity.required' => 'Masukkan jumlah stok yang ditambahkan.',
            'quantity.integer' => 'Jumlah stok harus berupa bilangan bulat.',
            'quantity.min' => 'Jumlah stok minimal 1.',
            'quantity.max' => 'Jumlah stok melebihi batas yang dapat disimpan.',
            'notes.string' => 'Catatan harus berupa teks.',
            'notes.max' => 'Catatan maksimal 2.000 karakter.',
        ];
    }
}
