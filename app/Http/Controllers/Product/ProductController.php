<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\IndexProductRequest;
use App\Http\Requests\Product\SaveProductRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(IndexProductRequest $request): Response
    {
        $filters = $request->validated();
        $status = $filters['status'] ?? 'active';
        $search = $filters['search'] ?? '';
        $owned = Product::whereHas('business', fn (Builder $query) => $query->where('user_id', $request->user()->id));

        return Inertia::render('Products/Index', [
            'products' => (clone $owned)->with(['variants' => fn (HasMany $variants) => $variants->orderBy('id')])
                ->when($status === 'active', fn (Builder $query) => $query->whereNull('archived_at'))
                ->when($status === 'archived', fn (Builder $query) => $query->whereNotNull('archived_at'))
                ->when($search !== '', fn (Builder $query) => $query->where('name', 'like', '%'.$search.'%'))
                ->orderByDesc('id')->paginate(12)->withQueryString(),
            'counts' => ['active' => (clone $owned)->whereNull('archived_at')->count(), 'archived' => (clone $owned)->whereNotNull('archived_at')->count()],
            'hasBusiness' => $request->user()->business()->exists(),
            'filters' => ['search' => $search, 'status' => $status],
            'status' => $request->session()->get('status'),
        ]);
    }

    public function store(SaveProductRequest $request, ProductService $service): RedirectResponse
    {
        return $this->save($request, $service);
    }

    public function update(SaveProductRequest $request, Product $product, ProductService $service): RedirectResponse
    {
        return $this->save($request, $service, $product);
    }

    public function archive(Request $request, Product $product, ProductService $service): RedirectResponse
    {
        Gate::authorize('archive', $product);
        try {
            $service->archive($request->user(), $product);
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['product' => 'Produk gagal diarsipkan. Silakan coba lagi.']);
        }

        return to_route('umkm.products.index')->with('status', 'Produk berhasil diarsipkan. Riwayat lama tetap tersedia.');
    }

    private function save(SaveProductRequest $request, ProductService $service, ?Product $product = null): RedirectResponse
    {
        try {
            $service->save($request->user(), $request->validated(), $product);
        } catch (QueryException $exception) {
            report($exception);

            return back()->withErrors(['product' => 'Produk gagal disimpan. Silakan coba lagi.']);
        }

        return to_route('umkm.products.index')->with('status', 'Produk berhasil disimpan.');
    }
}
