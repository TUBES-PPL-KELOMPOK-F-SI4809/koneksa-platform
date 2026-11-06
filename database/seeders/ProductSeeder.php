<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $products = [
            [
                'id' => 1,
                'business_id' => 1,
                'name' => 'Biji Kopi Arabika Gunung Puntang',
                'description' => 'Biji kopi single origin Arabika proses washed dari perkebunan ketinggian 1.400 mdpl Bandung Selatan.',
                'image_path' => null,
                'unit' => 'bungkus',
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'business_id' => 1,
                'name' => 'Cold Brew Concentrate Priangan',
                'description' => 'Konsentrat seduhan kopi dingin murni tahan simpan, nikmat dipadu susu oat atau es batu.',
                'image_path' => null,
                'unit' => 'botol',
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'business_id' => 2,
                'name' => 'Kemeja Shibori Katun Rayon',
                'description' => 'Busana etnik kasual lengan pendek dengan teknik celup shibori alami khas Sunda.',
                'image_path' => null,
                'unit' => 'pcs',
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'business_id' => 3,
                'name' => 'Lampu Meja Anyaman Bambu Cendani',
                'description' => 'Kap lampu hias meja ramah lingkungan dengan aksen pencahayaan hangat estetik.',
                'image_path' => null,
                'unit' => 'unit',
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(['id' => $product['id']], $product);
        }

        $variants = [
            [
                'id' => 1,
                'product_id' => 1,
                'variant_key' => 'biji-250g',
                'sku' => 'KOP-PUNT-250',
                'size' => '250 gram',
                'color' => null,
                'selling_price' => 85000.00,
                'stock_quantity' => 50,
                'low_stock_threshold' => 10,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'variant_key' => 'bubuk-250g',
                'sku' => 'KOP-PUNT-BUBUK',
                'size' => '250 gram',
                'color' => null,
                'selling_price' => 85000.00,
                'stock_quantity' => 40,
                'low_stock_threshold' => 10,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'product_id' => 2,
                'variant_key' => 'default',
                'sku' => 'KOP-COLD-500',
                'size' => '500 ml',
                'color' => null,
                'selling_price' => 45000.00,
                'stock_quantity' => 30,
                'low_stock_threshold' => 5,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'product_id' => 3,
                'variant_key' => 'indigo-m',
                'sku' => 'BTK-SHB-IND-M',
                'size' => 'M',
                'color' => 'Indigo Blue',
                'selling_price' => 195000.00,
                'stock_quantity' => 20,
                'low_stock_threshold' => 4,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'product_id' => 3,
                'variant_key' => 'indigo-l',
                'sku' => 'BTK-SHB-IND-L',
                'size' => 'L',
                'color' => 'Indigo Blue',
                'selling_price' => 195000.00,
                'stock_quantity' => 18,
                'low_stock_threshold' => 4,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'product_id' => 3,
                'variant_key' => 'terracotta-l',
                'sku' => 'BTK-SHB-TRC-L',
                'size' => 'L',
                'color' => 'Terracotta',
                'selling_price' => 195000.00,
                'stock_quantity' => 15,
                'low_stock_threshold' => 3,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 7,
                'product_id' => 4,
                'variant_key' => 'default',
                'sku' => 'BMB-LMP-01',
                'size' => 'Tinggi 35cm',
                'color' => 'Natural Bambu',
                'selling_price' => 165000.00,
                'stock_quantity' => 25,
                'low_stock_threshold' => 5,
                'archived_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->updateOrInsert(['id' => $variant['id']], $variant);
        }

        // Catat opening stock movement untuk setiap varian
        $movements = [
            ['id' => 1, 'product_variant_id' => 1, 'sale_id' => null, 'performed_by' => 3, 'type' => 'opening', 'quantity_delta' => 50, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 2, 'product_variant_id' => 2, 'sale_id' => null, 'performed_by' => 3, 'type' => 'opening', 'quantity_delta' => 40, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 3, 'product_variant_id' => 3, 'sale_id' => null, 'performed_by' => 3, 'type' => 'opening', 'quantity_delta' => 30, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 4, 'product_variant_id' => 4, 'sale_id' => null, 'performed_by' => 4, 'type' => 'opening', 'quantity_delta' => 20, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 5, 'product_variant_id' => 5, 'sale_id' => null, 'performed_by' => 4, 'type' => 'opening', 'quantity_delta' => 18, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 6, 'product_variant_id' => 6, 'sale_id' => null, 'performed_by' => 4, 'type' => 'opening', 'quantity_delta' => 15, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
            ['id' => 7, 'product_variant_id' => 7, 'sale_id' => null, 'performed_by' => 5, 'type' => 'opening', 'quantity_delta' => 25, 'notes' => 'Saldo stok awal sistem katalog', 'created_at' => $now],
        ];

        foreach ($movements as $movement) {
            DB::table('stock_movements')->updateOrInsert(['id' => $movement['id']], $movement);
        }
    }
}
