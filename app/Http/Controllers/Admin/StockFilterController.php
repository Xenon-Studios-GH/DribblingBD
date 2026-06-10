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

        if ($queryText) {
            $query->where(function ($q) use ($queryText) {
                $q->where('product_code', 'like', "%{$queryText}%")
                  ->orWhere('product_name', 'like', "%{$queryText}%");
            });
        }

        if ($filter === 'out_of_stock') {
            $query->having('total_stock', '=', 0);
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
