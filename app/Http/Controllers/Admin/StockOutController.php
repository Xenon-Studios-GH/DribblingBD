<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StockOutController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function index()
    {
        $products = Product::latest()->get(['id', 'product_code', 'product_name']);
        return view('stockout.index', compact('products'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size' => ['required', 'in:' . implode(',', Stock::SIZES)],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($validated['product_id']);

        try {
            $preview = $this->stockService->previewOut($product, $validated['size'], $validated['quantity']);
            return response()->json([
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'size' => $preview['size'],
                'current_stock' => $preview['current_stock'],
                'change' => $preview['change'],
                'new_stock' => $preview['new_stock'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'size' => ['required', 'in:' . implode(',', Stock::SIZES)],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        try {
            $product = Product::findOrFail($validated['product_id']);
            $this->stockService->stockOut($product, $validated['size'], $validated['quantity']);
            return response()->json(['success' => true, 'message' => 'Stock removed successfully.', 'product_id' => $product->id]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Stock out confirm failed', ['error' => $e->getMessage(), 'product_id' => $validated['product_id'] ?? null]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
        }
    }
}
