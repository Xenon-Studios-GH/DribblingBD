<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $newArrivals = Product::with('project')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest('updated_at')->take(8)->get();

        $topSelling = Product::with('project')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->withSum('stocks', 'quantity')
            ->orderByDesc('stocks_sum_quantity')
            ->take(8)->get();

        return view('shop.home', compact('newArrivals', 'topSelling'));
    }
}
