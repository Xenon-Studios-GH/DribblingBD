<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->get('q', '');

        if (empty($query)) {
            $products = Product::with('stocks')
                ->latest('updated_at')
                ->paginate(20);
        } else {
            $safeQuery = str_replace(['%', '_'], ['\\%', '\\_'], $query);
            $products = Product::where('product_code', 'like', "%{$safeQuery}%")
                ->orWhere('product_name', 'like', "%{$safeQuery}%")
                ->with('stocks')
                ->latest('updated_at')
                ->paginate(20);
        }

        $html = view('stock-management._table', compact('products'))->render();

        return response()->json(['html' => $html]);
    }
}
