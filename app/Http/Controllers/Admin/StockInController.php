<?php

namespace App\Http\Controllers\Admin;

use App\Enums\StockSize;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\StockService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StockInController extends Controller
{
    protected StockService $stockService;
    protected WorkLogService $workLogService;

    public function __construct(StockService $stockService, WorkLogService $workLogService)
    {
        $this->stockService = $stockService;
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $products = Product::with('stocks')->latest()->get(['id', 'product_code', 'product_name']);
        return view('stockin.index', compact('products'));
    }

    public function preview(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required'],
            'size' => ['required', 'in:' . implode(',', StockSize::values())],
            'quantity' => ['required', 'integer', 'min:1'],
            'product_name' => ['required_if:product_id,new', 'string', 'max:255'],
            'price' => ['required_if:product_id,new', 'numeric', 'min:0'],
        ]);

        if ($validated['product_id'] !== 'new') {
            $request->validate(['product_id' => 'exists:products,id']);
        }

        if ($validated['product_id'] === 'new') {
            return response()->json([
                'product_id' => 'new',
                'product_name' => $validated['product_name'],
                'price' => $validated['price'],
                'product_code' => '—',
                'size' => $validated['size'],
                'current_stock' => 0,
                'change' => (int) $validated['quantity'],
                'new_stock' => (int) $validated['quantity'],
            ]);
        }

        $product = Product::findOrFail($validated['product_id']);

        try {
            $preview = $this->stockService->previewIn($product, $validated['size'], $validated['quantity']);

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
            'product_id' => ['required', Rule::when($request->product_id !== 'new', 'exists:products,id')],
            'size' => ['required', 'in:' . implode(',', StockSize::values())],
            'quantity' => ['required', 'integer', 'min:1'],
            'product_name' => ['required_if:product_id,new', 'string', 'max:255'],
            'price' => ['required_if:product_id,new', 'numeric', 'min:0'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $product = DB::transaction(function () use ($validated) {
                if ($validated['product_id'] === 'new') {
                    $product = Product::create([
                        'product_code' => Product::generateProductCode(),
                        'product_name' => $validated['product_name'],
                        'price' => $validated['price'],
                    ]);

                    $this->workLogService->log(
                        'Product Created',
                        'stock',
                        $product->id,
                        "Product {$product->product_name} ({$product->product_code}) was created"
                    );
                } else {
                    $product = Product::findOrFail($validated['product_id']);
                }

                $this->stockService->stockIn($product, $validated['size'], $validated['quantity'], $validated['note'] ?? null);

                return $product;
            });
            return response()->json(['success' => true, 'message' => 'Stock added successfully.', 'product_id' => $product->id]);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            Log::error('Stock in confirm failed', ['error' => $e->getMessage(), 'product_id' => $validated['product_id'] ?? null]);
            return response()->json(['success' => false, 'message' => 'An unexpected error occurred.'], 500);
        }
    }
}
