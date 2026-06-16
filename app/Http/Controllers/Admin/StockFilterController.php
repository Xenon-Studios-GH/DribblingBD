<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockFilterController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Product::select('products.*')
            ->selectRaw('COALESCE((SELECT SUM(quantity) FROM stocks WHERE product_id = products.id), 0) as total_stock')
            ->with('stocks');

        $filter = $request->get('filter');
        $queryText = $request->get('q');
        $size = $request->get('size');

        if ($queryText) {
            $safeQueryText = str_replace(['%', '_'], ['\\%', '\\_'], $queryText);
            $query->where(function ($q) use ($safeQueryText) {
                $q->where('product_code', 'like', "%{$safeQueryText}%")
                  ->orWhere('product_name', 'like', "%{$safeQueryText}%");
            });
        }

        if ($size) {
            $query->whereHas('stocks', function ($q) use ($size) {
                $q->where('size', $size)->where('quantity', '>', 0);
            });
        }

        if ($filter === 'out_of_stock') {
            $query->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM stocks WHERE product_id = products.id) = 0');
        }

        $sort = match ($filter) {
            'stock_low' => 'stock_low',
            'stock_high' => 'stock_high',
            default => 'newest',
        };

        $query = match ($sort) {
            'stock_low' => $query->orderBy('total_stock'),
            'stock_high' => $query->orderByDesc('total_stock'),
            default => $query->orderByRaw('COALESCE((SELECT MAX(updated_at) FROM stocks WHERE product_id = products.id), products.updated_at) DESC'),
        };

        $products = $query->paginate(20);
        $html = view('stock-management._table', compact('products'))->render();

        return response()->json(['html' => $html]);
    }
}
