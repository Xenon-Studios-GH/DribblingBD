<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProfileController;
use App\Http\Controllers\Shop\CheckoutController;
use Illuminate\Support\Facades\Auth;

Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('shop/id/{product}', [ProductController::class, 'redirectById'])->name('products.by-id');
    Route::get('shop/{product:product_code}/{slug?}', [ProductController::class, 'show'])->name('products.show');
    Route::get('search', [ProductController::class, 'search'])->name('search');

    Route::view('user/cart', 'shop.cart.index')->name('cart.index');
    Route::get('user/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::get('user/order', function () { return redirect()->route('shop.checkout.index'); });
    Route::view('user/order/processing', 'shop.checkout.processing')->name('checkout.processing');
    Route::view('user/wishlist', 'shop.wishlist.index')->name('wishlist.index');

    Route::get('user', function () {
        if (Auth::check()) {
            $client = optional(Auth::user())->client;
            if ($client) {
                return redirect()->route('shop.profile.index', $client->usercode);
            }
        }
        return redirect()->route('authentication');
    })->name('user.home');
    Route::get('user/{usercode}/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::middleware('auth')->group(function () {
        Route::put('user/{usercode}/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('user/checkout/address', [CheckoutController::class, 'saveAddress'])->name('checkout.address.save');
    });

    // Website projects
    Route::get('/projects', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'index'])->name('projects');
    Route::get('/project/{categorySlug}/{subcategorySlug}/details/{projectSlug}', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'show'])->name('project.detail');
    Route::get('/category/{categorySlug}', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'category'])->name('category');
});
