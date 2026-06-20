<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;

class OrderStockController extends Controller
{
    public function productStock($productId)
    {
        $product = Product::with('stocks')->findOrFail($productId);
        $stockData = [];
        foreach (Stock::SIZES as $size) {
            $stock = $product->stocks->where('size', $size)->first();
            $stockData[$size] = $stock ? $stock->quantity : 0;
        }
        return response()->json([
            'id' => $product->id,
            'name' => $product->product_name,
            'price' => $product->price,
            'stocks' => $stockData,
        ]);
    }
}
