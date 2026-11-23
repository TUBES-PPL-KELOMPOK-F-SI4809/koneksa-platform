<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\StoreStockSaleRequest;
use App\Services\Inventory\SaleStockService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class StockSaleController extends Controller
{
    public function store(StoreStockSaleRequest $request, SaleStockService $service): JsonResponse
    {
        try {
            $sale = $service->record($request->user(), $request->validated('items'), $request->validated('notes'));
        } catch (QueryException $exception) {
            report($exception);

            return response()->json(['message' => 'Penjualan gagal disimpan. Silakan coba lagi.'], 500);
        }

        return response()->json(['message' => 'Penjualan tersimpan. Stok dan riwayat sudah diperbarui.', 'sale' => $sale], 201);
    }
}
