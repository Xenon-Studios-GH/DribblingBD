<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProfileController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\Shop\FaqController;
use App\Http\Controllers\Shop\CustomerCareController;

// Legacy redirects (301)
Route::permanentRedirect('/projects', '/shop');
Route::permanentRedirect('/category/{slug}', '/shop');
Route::get('/project/{categorySlug}/{subcategorySlug}/details/{projectSlug}', function ($catSlug, $subSlug, $projectSlug) {
    $project = \App\Models\WebsiteProject::where('slug', $projectSlug)->first();
    return redirect($project ? route('shop.products.show', $project) : '/shop', 301);
});

Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('/shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('/shop/{project:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/search', [ProductController::class, 'search'])->name('search');

    Route::view('/cart', 'shop.cart.index')->name('cart.index');
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:3,1')->name('checkout.store');
    Route::redirect('/order', '/checkout');
    Route::view('/order/processing', 'shop.checkout.processing')->name('checkout.processing');
    Route::view('/wishlist', 'shop.wishlist.index')->name('wishlist.index');

    Route::get('/user', [ProfileController::class, 'home'])->name('user.home');
    Route::get('/user/{usercode}/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::middleware('auth')->group(function () {
        Route::put('/user/{usercode}/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/checkout/address', [CheckoutController::class, 'saveAddress'])->middleware('throttle:10,1')->name('checkout.address.save');
    });

    Route::get('/faq', [FaqController::class, 'index'])->name('faq');
    Route::view('/size-guide', 'shop.size-guide.index')->name('size-guide');

    Route::get('/customer-care', [CustomerCareController::class, 'index'])->name('customer-care.index');
    Route::post('/customer-care', [CustomerCareController::class, 'store'])->middleware('throttle:5,10')->name('customer-care.store');
});
