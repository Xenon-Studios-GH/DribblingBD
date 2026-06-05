<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Finance\DashboardController;
use App\Http\Controllers\Admin\Finance\TransactionController;
use App\Http\Controllers\Admin\Finance\CategoryController;
use App\Http\Controllers\Admin\Finance\ReportController;
use App\Http\Controllers\Admin\Finance\NotificationController;

Route::middleware(['auth', 'role.match', 'role:superadmin,admin'])
    ->prefix('controlPanel/{role}/finance')
    ->name('finance.')
    ->group(function () {

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
    Route::get('transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    Route::get('reports', ReportController::class)->name('reports');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

Route::get('controlPanel/{role}/finance/notifications/unread-count', [NotificationController::class, 'unreadCount'])
    ->middleware(['auth', 'role.match'])
    ->name('finance.notifications.unread');
