<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $newArrivals = Product::with('project.images')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest('updated_at')->take(8)->get();

        $trending = Product::with('project.images')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->withCount('transactions')
            ->orderBy('transactions_count', 'desc')
            ->take(8)
            ->get();

        $heroImages = collect();
        for ($i = 1; $i <= 3; $i++) {
            $path = SiteSetting::getValue("hero_image_{$i}");
            if ($path) {
                $heroImages->push($path);
            }
        }

        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();

        return view('shop.home', compact('newArrivals', 'trending', 'heroImages', 'testimonials') + ['seoPage' => 'home']);
    }
}
