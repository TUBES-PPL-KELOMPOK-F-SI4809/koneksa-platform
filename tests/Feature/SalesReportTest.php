<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\BusinessCategory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_sales_report_page_renders_with_summary_and_date_filters(): void
    {
        [$owner, $business, $variant] = $this->setupUmkmWithSale(
            date: '2026-11-15 10:00:00',
            amount: '100000.00',
            qty: 5
        );

        $response = $this->actingAs($owner)->get(route('umkm.sales.report', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Sales/Report')
            ->where('summary.totalRevenue', fn ($val) => (float) $val === 100000.0)
            ->where('summary.totalSalesCount', 1)
            ->where('summary.totalItemsSold', 5)
            ->where('filters.start_date', '2026-11-01')
            ->where('filters.end_date', '2026-11-30')
            ->has('sales.data', 1)
        );
    }

    public function test_sales_report_filters_out_sales_outside_selected_date_range(): void
    {
        [$owner, $business] = $this->setupUmkmWithSale(
            date: '2026-10-10 10:00:00',
            amount: '50000.00'
        );

        $response = $this->actingAs($owner)->get(route('umkm.sales.report', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('summary.totalRevenue', 0)
            ->where('summary.totalSalesCount', 0)
            ->where('summary.totalItemsSold', 0)
            ->has('sales.data', 0)
        );
    }

    public function test_sales_report_is_isolated_to_authenticated_business_owner(): void
    {
        [$owner1, $business1] = $this->setupUmkmWithSale('2026-11-15 10:00:00', '30000.00');
        [$owner2, $business2] = $this->setupUmkmWithSale('2026-11-15 10:00:00', '80000.00');

        $this->actingAs($owner1)->get(route('umkm.sales.report', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->where('summary.totalRevenue', fn ($val) => (float) $val === 30000.0)
            ->where('summary.totalSalesCount', 1)
            ->where('sales.data.0.business_id', $business1->id)
        );
    }

    public function test_sales_report_can_be_streamed_as_csv_file(): void
    {
        [$owner, $business, $variant] = $this->setupUmkmWithSale(
            date: '2026-11-20 14:30:00',
            amount: '60000.00',
            qty: 3,
            notes: 'Catatan ekspor CSV'
        );

        $response = $this->actingAs($owner)->get(route('umkm.sales.report.export', [
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-30',
        ]));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('attachment; filename="rekap-penjualan-2026-11-01-sd-2026-11-30.csv"', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();

        $this->assertStringContainsString('No. Invoice', $content);
        $this->assertStringContainsString('Nama Produk', $content);
        $this->assertStringContainsString('Total Faktur (Rp)', $content);
        $this->assertStringContainsString('Kopi Priangan', $content);
        $this->assertStringContainsString('Catatan ekspor CSV', $content);
    }

    private function setupUmkmWithSale(string $date, string $amount, int $qty = 1, ?string $notes = null): array
    {
        $owner = User::factory()->umkm()->create();
        $category = BusinessCategory::firstOrCreate(['name' => 'Kuliner']);
        $business = Business::create([
            'user_id' => $owner->id,
            'business_category_id' => $category->id,
            'name' => 'Usaha '.$owner->id,
        ]);

        $product = Product::create([
            'business_id' => $business->id,
            'name' => 'Kopi Priangan',
            'unit' => 'bungkus',
        ]);

        $variant = $product->variants()->create([
            'variant_key' => 'default',
            'selling_price' => bcdiv($amount, (string) $qty, 2),
            'stock_quantity' => 20,
            'low_stock_threshold' => 2,
        ]);

        $sale = Sale::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-'.uniqid(),
            'sold_at' => Carbon::parse($date),
            'status' => 'recorded',
            'total_amount' => $amount,
            'notes' => $notes,
        ]);

        $sale->items()->create([
            'product_variant_id' => $variant->id,
            'product_name_snapshot' => $product->name,
            'variant_label_snapshot' => $variant->variant_key,
            'unit_snapshot' => $product->unit,
            'quantity' => $qty,
            'unit_price' => $variant->selling_price,
        ]);

        return [$owner, $business, $variant];
    }
}
