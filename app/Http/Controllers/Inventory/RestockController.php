<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreRestockRequest;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Services\Inventory\RestockService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RestockController extends Controller
{
    public function index(Request $request): Response
    {
        $ownedProduct = fn (Builder $query) => $query->whereHas('business',
            fn (Builder $business) => $business->where('user_id', $request->user()->id));

        return Inertia::render('Inventory/Index', [
            'variants' => ProductVariant::with('product:id,name,unit')
                ->whereHas('product', $ownedProduct)
                ->whereHas('product', fn (Builder $query) => $query->whereNull('archived_at'))
                ->whereNull('archived_at')
                ->select(['id', 'product_id', 'variant_key', 'sku', 'size', 'color', 'stock_quantity'])
                ->orderBy('product_id')->orderBy('id')
                ->paginate(12, pageName: 'stock_page')->withQueryString(),
            'movements' => StockMovement::with([
                'productVariant:id,product_id,variant_key,sku,size,color',
                'productVariant.product:id,name,unit',
                'performer:id,name',
            ])
                ->whereHas('productVariant.product', $ownedProduct)
                ->orderByDesc('created_at')->orderByDesc('id')
                ->paginate(15, pageName: 'history_page')->withQueryString(),
            'status' => $request->session()->get('status'),
        ]);
    }

    public function store(StoreRestockRequest $request, ProductVariant $variant, RestockService $service): RedirectResponse
    {
        try {
            $service->restock($variant, $request->user(), $request->integer('quantity'), $request->validated('notes'));
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['restock' => 'Restock gagal disimpan. Silakan coba lagi.']);
        }

        return to_route('umkm.inventory.index', $request->only('stock_page', 'history_page'))
            ->with('status', 'Restock berhasil dicatat. Stok dan riwayat sudah diperbarui.');
    }
}
