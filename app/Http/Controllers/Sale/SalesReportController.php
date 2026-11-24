<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesReportController extends Controller
{
    /**
     * KSA-26 FR-09.1 — Rekap penjualan berdasarkan periode / rentang tanggal.
     */
    public function index(Request $request): Response
    {
        $business = $request->user()->business;
        abort_unless($business, 403);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $validated['end_date'] ?? Carbon::now()->format('Y-m-d');

        $baseQuery = Sale::where('business_id', $business->id)
            ->where('status', 'recorded')
            ->where('sold_at', '>=', Carbon::parse($startDate)->startOfDay())
            ->where('sold_at', '<=', Carbon::parse($endDate)->endOfDay());

        $totalRevenue = (clone $baseQuery)->sum('total_amount');
        $totalSalesCount = (clone $baseQuery)->count();

        // Hitung total item terjual
        $totalItemsSold = (int) (clone $baseQuery)
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->sum('sale_items.quantity');

        $sales = (clone $baseQuery)
            ->with('items')
            ->orderByDesc('sold_at')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales/Report', [
            'sales' => $sales,
            'summary' => [
                'totalRevenue' => (float) $totalRevenue,
                'totalSalesCount' => $totalSalesCount,
                'totalItemsSold' => $totalItemsSold,
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * KSA-26 FR-09.2 — Ekspor laporan penjualan dalam format CSV streaming.
     */
    public function export(Request $request): StreamedResponse
    {
        $business = $request->user()->business;
        abort_unless($business, 403);

        $validated = $request->validate([
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:start_date'],
        ]);

        $startDate = $validated['start_date'] ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = $validated['end_date'] ?? Carbon::now()->format('Y-m-d');

        $fileName = "rekap-penjualan-{$startDate}-sd-{$endDate}.csv";

        $sales = Sale::where('business_id', $business->id)
            ->where('status', 'recorded')
            ->where('sold_at', '>=', Carbon::parse($startDate)->startOfDay())
            ->where('sold_at', '<=', Carbon::parse($endDate)->endOfDay())
            ->with('items')
            ->orderBy('sold_at')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($sales) {
            $handle = fopen('php://output', 'w');

            // BOM untuk dukungan karakter UTF-8 di Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header kolom CSV
            fputcsv($handle, [
                'No. Invoice',
                'Tanggal Penjualan (WIB)',
                'Nama Produk',
                'Varian',
                'Satuan',
                'Jumlah (Qty)',
                'Harga Satuan (Rp)',
                'Subtotal (Rp)',
                'Total Faktur (Rp)',
                'Catatan',
            ]);

            foreach ($sales as $sale) {
                $soldAtFormatted = Carbon::parse($sale->sold_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i:s');

                if ($sale->items->isEmpty()) {
                    fputcsv($handle, [
                        $sale->invoice_number,
                        $soldAtFormatted,
                        '-',
                        '-',
                        '-',
                        0,
                        '0.00',
                        '0.00',
                        $sale->total_amount,
                        $sale->notes ?? '',
                    ]);

                    continue;
                }

                foreach ($sale->items as $index => $item) {
                    $subtotal = number_format((float) $item->unit_price * $item->quantity, 2, '.', '');
                    fputcsv($handle, [
                        $sale->invoice_number,
                        $soldAtFormatted,
                        $item->product_name_snapshot,
                        $item->variant_label_snapshot === 'default' ? 'Standar' : $item->variant_label_snapshot,
                        $item->unit_snapshot,
                        $item->quantity,
                        $item->unit_price,
                        $subtotal,
                        $index === 0 ? $sale->total_amount : '',
                        $index === 0 ? ($sale->notes ?? '') : '',
                    ]);
                }
            }

            fclose($handle);
        }, 200, $headers);
    }
}
