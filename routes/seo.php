<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SeoController;

Route::middleware(['auth', 'role.match', 'role:superadmin,admin'])
    ->prefix('controlPanel/{role}/seo')
    ->name('seo.')
    ->group(function () {

    Route::get('/', [SeoController::class, 'index'])->name('index');
    Route::get('{seoMeta}/edit', [SeoController::class, 'edit'])->name('edit');
    Route::put('{seoMeta}', [SeoController::class, 'update'])->name('update');
    Route::delete('{seoMeta}', [SeoController::class, 'destroy'])->name('destroy');
    Route::get('{seoMeta}/reset-template', [SeoController::class, 'resetTemplate'])->name('reset-template');
    Route::post('auto-generate', [SeoController::class, 'autoGenerate'])->name('auto-generate');
});
