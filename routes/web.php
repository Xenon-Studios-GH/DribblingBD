<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\StockSearchController;
use App\Http\Controllers\Admin\StockFilterController;
use App\Http\Controllers\Admin\StockActivityController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\LoginLogController;
use App\Http\Controllers\Admin\WorkLogController;

Route::prefix('auth/dribblingbd')->middleware('guest')->group(function () {
    Route::get('authentication', fn() => view('auth.authentication'))->name('authentication');
    Route::get('login', fn() => redirect()->route('authentication'))->name('login');
    Route::post('login', [LoginController::class, 'store'])->middleware('throttle:5,1');
    Route::get('register', fn() => redirect()->route('authentication'))->name('register');
    Route::post('register', [\App\Http\Controllers\Shop\Auth\RegisterController::class, 'store']);
});

Route::middleware('guest')->group(function () {
    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email')->middleware('throttle:3,1');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update')->middleware('throttle:3,1');
});

Route::middleware('auth')->group(function () {
    Route::post('auth/dribblingbd/logout', LogoutController::class)->name('logout');

    Route::prefix('controlPanel/{role}')->where(['role' => 'superadmin|admin|staff|manager'])->middleware('role.match')->group(function () {

        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Stock management
        Route::get('stock-management', StockManagementController::class)->name('stock.management');
        Route::get('stock-management/{product}', [StockManagementController::class, 'show'])->name('stock.management.show');
        Route::get('stock-management/{product}/transactions', [StockManagementController::class, 'transactions'])->name('stock.management.transactions');
        Route::get('stock/search', StockSearchController::class)->name('stock.search');
        Route::get('stock/filter', StockFilterController::class)->name('stock.filter');
        Route::get('stock-activity', StockActivityController::class)->name('stock.activity');

        // Stock In
        Route::get('stockin', [StockInController::class, 'index'])->name('stock.in');
        Route::post('stockin/preview', [StockInController::class, 'preview'])->middleware('throttle:30,1')->name('stock.in.preview');
        Route::post('stockin/confirm', [StockInController::class, 'confirm'])->middleware('throttle:20,1')->name('stock.in.confirm');

        // Stock Out
        Route::get('stockout', [StockOutController::class, 'index'])->name('stock.out');
        Route::post('stockout/preview', [StockOutController::class, 'preview'])->middleware('throttle:30,1')->name('stock.out.preview');
        Route::post('stockout/confirm', [StockOutController::class, 'confirm'])->middleware('throttle:20,1')->name('stock.out.confirm');

        // Product
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('stock/products', [ProductController::class, 'store'])->name('stock.products.store');
            Route::put('stock/products/{product}', [ProductController::class, 'update'])->name('stock.products.update');
        });

        Route::middleware('role:superadmin')->group(function () {
            Route::get('workers', [WorkerController::class, 'index'])->name('workers.index');
            Route::get('workers/create', [WorkerController::class, 'create'])->name('workers.create');
            Route::post('workers', [WorkerController::class, 'store'])->name('workers.store');
            Route::get('workers/{worker}/edit', [WorkerController::class, 'edit'])->name('workers.edit');
            Route::put('workers/{worker}', [WorkerController::class, 'update'])->name('workers.update');
            Route::post('workers/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
            Route::get('login-logs', [LoginLogController::class, 'index'])->name('login-logs.index');
            Route::get('work-logs', [WorkLogController::class, 'index'])->name('work-logs.index');
        });

        // Finance
        Route::middleware('role:superadmin,admin')->prefix('finance')->name('finance.')->group(function () {
            Route::get('dashboard', \App\Http\Controllers\Admin\Finance\DashboardController::class)->name('dashboard');
            Route::get('transactions', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'index'])->name('transactions');
            Route::get('transactions/create', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'create'])->name('transactions.create');
            Route::post('transactions', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'store'])->name('transactions.store');
            Route::get('transactions/{transaction}/edit', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'edit'])->name('transactions.edit');
            Route::put('transactions/{transaction}', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'update'])->name('transactions.update');
            Route::delete('transactions/{transaction}', [\App\Http\Controllers\Admin\Finance\TransactionController::class, 'destroy'])->name('transactions.destroy');
            Route::get('categories', [\App\Http\Controllers\Admin\Finance\CategoryController::class, 'index'])->name('categories');
            Route::post('categories', [\App\Http\Controllers\Admin\Finance\CategoryController::class, 'store'])->name('categories.store');
            Route::put('categories/{category}', [\App\Http\Controllers\Admin\Finance\CategoryController::class, 'update'])->name('categories.update');
            Route::delete('categories/{category}', [\App\Http\Controllers\Admin\Finance\CategoryController::class, 'destroy'])->name('categories.destroy');
            Route::get('projects', [\App\Http\Controllers\Admin\Finance\ProjectController::class, 'index'])->name('projects');
            Route::post('projects', [\App\Http\Controllers\Admin\Finance\ProjectController::class, 'store'])->name('projects.store');
            Route::put('projects/{project}', [\App\Http\Controllers\Admin\Finance\ProjectController::class, 'update'])->name('projects.update');
            Route::delete('projects/{project}', [\App\Http\Controllers\Admin\Finance\ProjectController::class, 'destroy'])->name('projects.destroy');
            Route::get('reports', \App\Http\Controllers\Admin\Finance\ReportController::class)->name('reports');
            Route::get('notifications', [\App\Http\Controllers\Admin\Finance\NotificationController::class, 'index'])->name('notifications');
            Route::post('notifications/{notification}/read', [\App\Http\Controllers\Admin\Finance\NotificationController::class, 'markAsRead'])->name('notifications.read');
            Route::post('notifications/mark-all-read', [\App\Http\Controllers\Admin\Finance\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
        });

    });
});

Route::get('controlPanel/{role}/finance/notifications/unread-count', [\App\Http\Controllers\Admin\Finance\NotificationController::class, 'unreadCount'])
    ->middleware(['auth', 'role.match'])
    ->name('finance.notifications.unread');

require __DIR__.'/shop.php';
