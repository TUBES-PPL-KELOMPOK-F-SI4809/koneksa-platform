<?php

namespace App\Http\Requests\Inventory;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class StoreStockSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'min:1', 'max:50'],
            'items.*' => ['required', 'array:product_variant_id,quantity'],
            'items.*.product_variant_id' => ['required', 'integer', 'min:1', 'distinct'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:2147483647'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'integer' => ':attribute harus berupa bilangan bulat.',
            'min' => ':attribute minimal :min.',
            'max' => ':attribute melebihi batas yang diizinkan.',
            'array' => ':attribute tidak sesuai format.',
            'string' => ':attribute harus berupa teks.',
            'distinct' => 'Setiap varian hanya boleh dicatat satu kali dalam transaksi.',
        ];
    }

    public function attributes(): array
    {
        return ['items' => 'Daftar barang', 'items.*' => 'Barang', 'items.*.product_variant_id' => 'Varian produk', 'items.*.quantity' => 'Jumlah penjualan', 'notes' => 'Catatan'];
    }
}
