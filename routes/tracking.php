<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Tracking\TrackingPixelController;
use App\Http\Controllers\Admin\Tracking\TrackingEventLogController;
use App\Http\Controllers\Admin\Tracking\TrackingDiagnosticsController;

Route::middleware(['auth', 'role:superadmin,admin'])
    ->prefix('controlPanel/tracking')
    ->name('tracking.')
    ->group(function () {

    Route::get('/', [TrackingPixelController::class, 'index'])->name('index');
    Route::get('create', [TrackingPixelController::class, 'create'])->name('create');
    Route::post('/', [TrackingPixelController::class, 'store'])->name('store');
    Route::get('{trackingPixel}/edit', [TrackingPixelController::class, 'edit'])->name('edit');
    Route::put('{trackingPixel}', [TrackingPixelController::class, 'update'])->name('update');
    Route::delete('{trackingPixel}', [TrackingPixelController::class, 'destroy'])->name('destroy');
    Route::post('{trackingPixel}/toggle', [TrackingPixelController::class, 'toggle'])->name('toggle');

    Route::get('events', [TrackingEventLogController::class, 'index'])->name('events');
    Route::post('events/{trackingEventLog}/retry', [TrackingEventLogController::class, 'retry'])->name('events.retry');

    Route::get('diagnostics', [TrackingDiagnosticsController::class, 'index'])->name('diagnostics');
    Route::post('diagnostics/test/{trackingPixel}', [TrackingDiagnosticsController::class, 'testEvent'])->name('diagnostics.test');
    Route::post('diagnostics/toggle-debug', [TrackingDiagnosticsController::class, 'toggleDebug'])->name('diagnostics.toggle-debug');
});
