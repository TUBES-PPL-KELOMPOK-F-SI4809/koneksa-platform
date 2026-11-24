<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class StockDashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        Gate::authorize('viewAny', Product::class);

        $user = $request->user();
        $businessId = $user->business?->id;

        $variants = ProductVariant::availableForSale()->whereHas('product.business',
            fn (Builder $query) => $query->where('user_id', $user->id));
        $lowStock = (clone $variants)->lowStock();

        // KSA-25 FR-08.1 — Agregasi Omzet & Total Transaksi
        $salesQuery = Sale::where('business_id', $businessId)->where('status', 'recorded');
        $totalRevenue = (clone $salesQuery)->sum('total_amount');
        $totalSalesCount = (clone $salesQuery)->count();

        // Tren penjualan 7 hari terakhir untuk visual bento dashboard
        $startDate = Carbon::today()->subDays(6);
        $recentSales = (clone $salesQuery)
            ->where('sold_at', '>=', $startDate->startOfDay())
            ->get(['sold_at', 'total_amount']);

        $salesTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dayLabel = match ($date->dayOfWeek) {
                0 => 'Min', 1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab'
            };
            $daySales = $recentSales->filter(fn ($sale) => Carbon::parse($sale->sold_at)->isSameDay($date));
            $salesTrend[] = [
                'day' => $dayLabel,
                'date' => $date->format('d/m'),
                'amount' => (float) $daySales->sum('total_amount'),
                'count' => $daySales->count(),
            ];
        }

        return Inertia::render('Umkm/Dashboard', [
            'counts' => [
                'active' => (clone $variants)->count(),
                'low' => (clone $lowStock)->count(),
                'out' => (clone $variants)->where('stock_quantity', 0)->count(),
            ],
            'financials' => [
                'totalRevenue' => (float) $totalRevenue,
                'totalSalesCount' => $totalSalesCount,
            ],
            'salesTrend' => $salesTrend,
            'lowStockVariants' => $lowStock->with('product:id,name,unit')
                ->orderBy('stock_quantity')->orderBy('id')->limit(5)
                ->get(['id', 'product_id', 'variant_key', 'stock_quantity', 'low_stock_threshold'])
                ->each->append('stock_status'),
            'hasBusiness' => $user->business()->exists(),
        ]);
    }
}
