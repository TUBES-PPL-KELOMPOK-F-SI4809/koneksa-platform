<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SaveProductRequest extends FormRequest
{
    public function authorize(): Response
    {
        $product = $this->route('product');

        return $product ? Gate::inspect('update', $product) : Gate::inspect('create', Product::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:10000'],
            'unit' => ['required', 'string', 'max:50'],
            'variants' => ['required', 'array', 'min:1', 'max:50'],
            'variants.*' => ['required', 'array:id,variant_key,sku,size,color,selling_price,low_stock_threshold'],
            'variants.*.id' => ['nullable', 'integer', 'min:1', 'distinct'],
            'variants.*.variant_key' => ['required', 'string', 'max:100', 'distinct:ignore_case'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.selling_price' => ['required', 'regex:/^\d{1,13}(\.\d{1,2})?$/'],
            'variants.*.low_stock_threshold' => ['required', 'integer', 'min:0', 'max:2147483647'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'string' => ':attribute harus berupa teks.',
            'max' => ':attribute melebihi batas yang diizinkan.',
            'variants.*.selling_price.regex' => 'Harga harus nol atau positif, maksimal 13 digit dan 2 angka desimal.',
            'variants.*.variant_key.distinct' => 'Nama varian tidak boleh sama.',
            'variants.*.low_stock_threshold.integer' => 'Batas stok harus berupa bilangan bulat.',
            'variants.*.low_stock_threshold.min' => 'Batas stok tidak boleh negatif.',
        ];
    }

    public function attributes(): array
    {
        return ['name' => 'Nama produk', 'unit' => 'Satuan', 'description' => 'Deskripsi',
            'variants' => 'Varian', 'variants.*.variant_key' => 'Nama varian',
            'variants.*.selling_price' => 'Harga', 'variants.*.low_stock_threshold' => 'Batas stok'];
    }
}
