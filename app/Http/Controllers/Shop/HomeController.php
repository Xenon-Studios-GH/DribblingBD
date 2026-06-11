<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Testimonial;
use App\Models\WebsiteProjectImage;

class HomeController extends Controller
{
    public function index()
    {
        $newArrivals = Product::with('project')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest('updated_at')->take(8)->get();

        $trending = Product::with('project')
            ->where('is_active', true)
            ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ->latest('updated_at')
            ->take(8)->get();

        $heroImages = WebsiteProjectImage::with('project.product')
            ->whereHas('project.product', fn($q) => $q->where('is_active', true))
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->pluck('image_path');

        $testimonials = Testimonial::where('is_active', true)->orderBy('sort_order')->get();

        return view('shop.home', compact('newArrivals', 'trending', 'heroImages', 'testimonials'));
    }
}
