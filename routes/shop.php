<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Shop\HomeController;
use App\Http\Controllers\Shop\ProductController;
use App\Http\Controllers\Shop\ProfileController;

Route::name('shop.')->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::get('shop', [ProductController::class, 'index'])->name('products.index');
    Route::get('shop/{product:product_code}', [ProductController::class, 'show'])->name('products.show');
    Route::get('search', [ProductController::class, 'search'])->name('search');

    Route::view('cart', 'shop.cart.index')->name('cart.index');
    Route::view('wishlist', 'shop.wishlist.index')->name('wishlist.index');

    Route::get('user/client/{usercode}', [ProfileController::class, 'index'])->name('profile.index');

    Route::middleware('auth')->group(function () {
        Route::put('user/client/{usercode}', [ProfileController::class, 'update'])->name('profile.update');
    });

    // Website projects
    Route::get('/projects', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'index'])->name('projects');
    Route::get('/project/{categorySlug}/{subcategorySlug}/details/{projectSlug}', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'show'])->name('project.detail');
    Route::get('/category/{categorySlug}', [\App\Http\Controllers\Shop\WebsiteProjectController::class, 'category'])->name('category');
});
