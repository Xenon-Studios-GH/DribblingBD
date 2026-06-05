<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product = DB::transaction(function () use ($validated) {
            $validated['product_code'] = Product::generateProductCode();

            $product = Product::create($validated);

            $this->workLogService->log(
                'Product Created',
                'stock',
                $product->id,
                "Product {$product->product_name} ({$product->product_code}) was created"
            );

            return $product;
        });

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $product->id,
                'product_code' => $product->product_code,
                'product_name' => $product->product_name,
                'price' => $product->price,
            ]);
        }

        return redirect(admin_route('stock.management'))
            ->with('success', "Product {$product->product_name} created successfully.");
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update($validated);

        $this->workLogService->log(
            'Product Updated',
            'stock',
            $product->id,
            "Product {$product->product_name} was updated"
        );

        return back()->with('success', 'Product updated successfully.');
    }
}
