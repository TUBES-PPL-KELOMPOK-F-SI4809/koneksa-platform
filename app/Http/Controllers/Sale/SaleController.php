<?php

namespace App\Http\Controllers\Sale;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockSaleRequest;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Services\Inventory\SaleStockService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    /**
     * KSA-23 FR-07.1 — Halaman kasir: pilih produk, kuantitas, hitung total.
     */
    public function create(Request $request): Response
    {
        $variants = ProductVariant::with('product:id,name,unit')
            ->availableForSale()
            ->whereHas('product.business',
                fn (Builder $query) => $query->where('user_id', $request->user()->id))
            ->where('stock_quantity', '>', 0)
            ->select(['id', 'product_id', 'variant_key', 'sku', 'selling_price', 'stock_quantity'])
            ->orderBy('product_id')->orderBy('id')
            ->get();

        return Inertia::render('Sales/Create', [
            'variants' => $variants,
            'hasBusiness' => $request->user()->business()->exists(),
        ]);
    }

    /**
     * KSA-23 + KSA-24 FR-07.1/FR-07.2 — Simpan transaksi penjualan secara atomik.
     */
    public function store(StoreStockSaleRequest $request, SaleStockService $service): RedirectResponse
    {
        try {
            $service->record($request->user(), $request->validated('items'), $request->validated('notes'));
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['sale' => 'Penjualan gagal disimpan. Silakan coba lagi.']);
        }

        return to_route('umkm.sales.index')
            ->with('status', 'Penjualan berhasil dicatat. Stok dan riwayat sudah diperbarui.');
    }

    /**
     * KSA-24 FR-07.2 — Riwayat transaksi penjualan.
     */
    public function index(Request $request): Response
    {
        $sales = Sale::with('items')
            ->whereHas('business',
                fn (Builder $query) => $query->where('user_id', $request->user()->id))
            ->orderByDesc('sold_at')->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'status' => $request->session()->get('status'),
        ]);
    }

    /**
     * KSA-24 FR-07.2 — Detail satu transaksi penjualan.
     */
    public function show(Request $request, Sale $sale): Response
    {
        $sale->load('items');
        $business = $request->user()->business;
        abort_unless($business && $sale->business_id === $business->id, 403);

        return Inertia::render('Sales/Show', [
            'sale' => $sale,
        ]);
    }
}
