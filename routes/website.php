<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Website\DashboardController;
use App\Http\Controllers\Admin\Website\ProjectController;
use App\Http\Controllers\Admin\Website\CategoryController;

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

    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});
