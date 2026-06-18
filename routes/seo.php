<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RedirectController;
use App\Http\Controllers\Admin\SeoController;

Route::middleware(['auth', 'role:superadmin,admin'])
    ->prefix('controlPanel/seo')
    ->name('seo.')
    ->group(function () {

    Route::get('/', [SeoController::class, 'index'])->name('index');
    Route::get('{seoMeta}/edit', [SeoController::class, 'edit'])->name('edit');
    Route::put('{seoMeta}', [SeoController::class, 'update'])->name('update');
    Route::delete('{seoMeta}', [SeoController::class, 'destroy'])->name('destroy');
    Route::get('{seoMeta}/reset-template', [SeoController::class, 'resetTemplate'])->name('reset-template');
    Route::post('auto-generate', [SeoController::class, 'autoGenerate'])->name('auto-generate');

    Route::get('dashboard', [SeoController::class, 'dashboard'])->name('dashboard');
    Route::post('audit', [SeoController::class, 'runAudit'])->name('audit');

    Route::get('settings', [SeoController::class, 'settings'])->name('settings');
    Route::post('settings', [SeoController::class, 'updateSettings'])->name('settings.update');

    Route::prefix('redirects')->name('redirects.')->group(function () {
        Route::get('/', [RedirectController::class, 'index'])->name('index');
        Route::get('create', [RedirectController::class, 'create'])->name('create');
        Route::post('/', [RedirectController::class, 'store'])->name('store');
        Route::get('{redirect}/edit', [RedirectController::class, 'edit'])->name('edit');
        Route::put('{redirect}', [RedirectController::class, 'update'])->name('update');
        Route::delete('{redirect}', [RedirectController::class, 'destroy'])->name('destroy');
    });
});
