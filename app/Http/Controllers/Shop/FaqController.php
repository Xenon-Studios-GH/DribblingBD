<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $productFaqs = Faq::where('category', 'product')->where('is_active', true)->orderBy('sort_order')->get();
        $orderFaqs = Faq::where('category', 'order')->where('is_active', true)->orderBy('sort_order')->get();
        return view('shop.faq.index', compact('productFaqs', 'orderFaqs'));
    }
}
