<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $yesterday = now()->subDay();

        $sales = [
            [
                'id' => 1,
                'business_id' => 1,
                'invoice_number' => 'INV-20261105-0001',
                'sold_at' => $yesterday,
                'status' => 'recorded',
                'total_amount' => 215000.00,
                'notes' => 'Penjualan ritel langsung di kedai Braga',
                'cancelled_at' => null,
                'created_at' => $yesterday,
                'updated_at' => $yesterday,
            ],
            [
                'id' => 2,
                'business_id' => 2,
                'invoice_number' => 'INV-20261106-0002',
                'sold_at' => $now,
                'status' => 'recorded',
                'total_amount' => 195000.00,
                'notes' => 'Pesanan pelanggan butik via WhatsApp',
                'cancelled_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($sales as $sale) {
            DB::table('sales')->updateOrInsert(['id' => $sale['id']], $sale);
        }

        $saleItems = [
            [
                'id' => 1,
                'sale_id' => 1,
                'product_variant_id' => 1,
                'product_name_snapshot' => 'Biji Kopi Arabika Gunung Puntang',
                'variant_label_snapshot' => '250 gram',
                'unit_snapshot' => 'bungkus',
                'quantity' => 2,
                'unit_price' => 85000.00,
                'created_at' => $yesterday,
                'updated_at' => $yesterday,
            ],
            [
                'id' => 2,
                'sale_id' => 1,
                'product_variant_id' => 3,
                'product_name_snapshot' => 'Cold Brew Concentrate Priangan',
                'variant_label_snapshot' => '500 ml',
                'unit_snapshot' => 'botol',
                'quantity' => 1,
                'unit_price' => 45000.00,
                'created_at' => $yesterday,
                'updated_at' => $yesterday,
            ],
            [
                'id' => 3,
                'sale_id' => 2,
                'product_variant_id' => 4,
                'product_name_snapshot' => 'Kemeja Shibori Katun Rayon',
                'variant_label_snapshot' => 'Size M / Indigo Blue',
                'unit_snapshot' => 'pcs',
                'quantity' => 1,
                'unit_price' => 195000.00,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($saleItems as $item) {
            DB::table('sale_items')->updateOrInsert(['id' => $item['id']], $item);
        }

        // Catat pergerakan stok penjualan
        $saleMovements = [
            [
                'id' => 8,
                'product_variant_id' => 1,
                'sale_id' => 1,
                'performed_by' => 3,
                'type' => 'sale',
                'quantity_delta' => -2,
                'notes' => 'Pengurangan stok transaksi INV-20261105-0001',
                'created_at' => $yesterday,
            ],
            [
                'id' => 9,
                'product_variant_id' => 3,
                'sale_id' => 1,
                'performed_by' => 3,
                'type' => 'sale',
                'quantity_delta' => -1,
                'notes' => 'Pengurangan stok transaksi INV-20261105-0001',
                'created_at' => $yesterday,
            ],
            [
                'id' => 10,
                'product_variant_id' => 4,
                'sale_id' => 2,
                'performed_by' => 4,
                'type' => 'sale',
                'quantity_delta' => -1,
                'notes' => 'Pengurangan stok transaksi INV-20261106-0002',
                'created_at' => $now,
            ],
        ];

        foreach ($saleMovements as $movement) {
            DB::table('stock_movements')->updateOrInsert(['id' => $movement['id']], $movement);
        }
    }
}
