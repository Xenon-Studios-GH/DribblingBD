<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProfileController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\FaqController;
use App\Http\Controllers\Shop\CustomerCareController;
use App\Http\Controllers\Shop\WebsiteProjectController;

Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('shop/id/{product}', [ProductController::class, 'redirectById'])->name('products.by-id');
    Route::get('shop/{product:product_code}/{slug?}', [ProductController::class, 'show'])->name('products.show');
    Route::get('search', [ProductController::class, 'search'])->name('search');

    Route::view('user/cart', 'shop.cart.index')->name('cart.index');
    Route::get('user/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::redirect('user/order', 'user/checkout');
    Route::view('user/order/processing', 'shop.checkout.processing')->name('checkout.processing');
    Route::view('user/wishlist', 'shop.wishlist.index')->name('wishlist.index');

    Route::get('user', [ProfileController::class, 'home'])->name('user.home');
    Route::get('user/{usercode}/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::middleware('auth')->group(function () {
        Route::put('user/{usercode}/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('user/checkout/address', [CheckoutController::class, 'saveAddress'])->name('checkout.address.save');
        Route::post('user/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    });

    Route::get('faq', [FaqController::class, 'index'])->name('faq');

    Route::view('size-guide', 'shop.size-guide.index')->name('size-guide');

    Route::get('customer-care', [CustomerCareController::class, 'index'])->name('customer-care.index');
    Route::post('customer-care', [CustomerCareController::class, 'store'])->name('customer-care.store');

    // Website projects
    Route::get('/projects', [WebsiteProjectController::class, 'index'])->name('projects');
    Route::get('/project/{categorySlug}/{subcategorySlug}/details/{projectSlug}', [WebsiteProjectController::class, 'show'])->name('project.detail');
    Route::get('/category/{categorySlug}', [WebsiteProjectController::class, 'category'])->name('category');
});
