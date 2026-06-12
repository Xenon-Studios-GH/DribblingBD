<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Website\DashboardController;
use App\Http\Controllers\Admin\Website\ProjectController;
use App\Http\Controllers\Admin\Website\CategoryController;
use App\Http\Controllers\Admin\Website\CustomizationController;

Route::middleware(['auth', 'role.match', 'role:superadmin,admin'])
    ->prefix('controlPanel/{role}/website')
    ->name('website.')
    ->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('projects', [ProjectController::class, 'index'])->name('projects');
    Route::get('projects/create-from-product/{product}', [ProjectController::class, 'createFromProduct'])->name('projects.create-from-product');
    Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('projects/{project}/toggle-active', [ProjectController::class, 'toggleActive'])->name('projects.toggle-active');
    Route::post('products/{product}/toggle-active', [ProjectController::class, 'toggleProductActive'])->name('products.toggle-active');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::prefix('customization')->name('customization.')->group(function () {
        Route::get('/', [CustomizationController::class, 'index'])->name('index');

        Route::get('faqs/{faq}', [CustomizationController::class, 'getFaq'])->name('faqs.get');
        Route::post('faqs', [CustomizationController::class, 'storeFaq'])->name('faqs.store');
        Route::put('faqs/{faq}', [CustomizationController::class, 'updateFaq'])->name('faqs.update');
        Route::delete('faqs/{faq}', [CustomizationController::class, 'destroyFaq'])->name('faqs.destroy');

        Route::get('testimonials/{testimonial}', [CustomizationController::class, 'getTestimonial'])->name('testimonials.get');
        Route::post('testimonials', [CustomizationController::class, 'storeTestimonial'])->name('testimonials.store');
        Route::post('testimonials/{testimonial}', [CustomizationController::class, 'updateTestimonial'])->name('testimonials.update');
        Route::delete('testimonials/{testimonial}', [CustomizationController::class, 'destroyTestimonial'])->name('testimonials.destroy');

        Route::post('settings', [CustomizationController::class, 'updateSettings'])->name('settings.update');
    });
});
