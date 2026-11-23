<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
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
        $variants = ProductVariant::availableForSale()->whereHas('product.business',
            fn (Builder $query) => $query->where('user_id', $request->user()->id));
        $lowStock = (clone $variants)->lowStock();

        return Inertia::render('Umkm/Dashboard', [
            'counts' => [
                'active' => (clone $variants)->count(),
                'low' => (clone $lowStock)->count(),
                'out' => (clone $variants)->where('stock_quantity', 0)->count(),
            ],
            'lowStockVariants' => $lowStock->with('product:id,name,unit')
                ->orderBy('stock_quantity')->orderBy('id')->limit(5)
                ->get(['id', 'product_id', 'variant_key', 'stock_quantity', 'low_stock_threshold'])
                ->each->append('stock_status'),
            'hasBusiness' => $request->user()->business()->exists(),
        ]);
    }
}
