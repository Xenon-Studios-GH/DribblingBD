<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $newArrivals = Product::with('project')
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest()->take(8)->get();

        $topSelling = Product::with('project')
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->withSum('stocks', 'quantity')
            ->orderBy('stocks_sum_quantity')
            ->take(8)->get();

        return view('shop.home', compact('newArrivals', 'topSelling'));
    }
}
