<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\Auth\LogoutController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\StockManagementController;
use App\Http\Controllers\Admin\StockInController;
use App\Http\Controllers\Admin\StockOutController;
use App\Http\Controllers\Admin\StockSearchController;
use App\Http\Controllers\Admin\StockFilterController;
use App\Http\Controllers\Admin\StockReportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\WorkerController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\OrderCreateController;
use App\Http\Controllers\Admin\OrderEditController;
use App\Http\Controllers\Admin\OrderDeleteController;
use App\Http\Controllers\Admin\OrderStatusController;
use App\Http\Controllers\Admin\OrderStockController;
use App\Http\Controllers\Admin\OrderDraftController;
use App\Http\Controllers\Shop\Auth\RegisterController;

Route::middleware('guest')->group(function () {
    Route::view('authentication', 'auth.authentication')->name('authentication');
    Route::post('login', [LoginController::class, 'store'])->name('login');
    Route::post('register', [RegisterController::class, 'store'])->name('register')->middleware('throttle:5,10');
    Route::get('check-email/{email}', function (string $email) {
        $exists = \App\Models\User::where('email', $email)->exists();
        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'Email found' : 'Email not found',
        ]);
    })->name('check-email')->where('email', '.*')->middleware('throttle:10,1');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email')->middleware('throttle:3,1');
    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update')->middleware('throttle:3,1');
});

// Trap session page
Route::get('xxxxx', function () {
    if (!session()->has('trap_session')) {
        return redirect()->route('authentication');
    }
    return view('trap.xxxxx');
})->name('trap.page')->middleware('web');

Route::middleware(['auth', 'trap'])->group(function () {
    Route::post('logout', LogoutController::class)->name('logout');

    Route::prefix('controlPanel')->group(function () {

        Route::get('dashboard', DashboardController::class)->name('dashboard');

        // Reports & Analytics
        Route::prefix('reports-and-analytics')->name('admin.reports')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('.index');
            Route::get('/data', [\App\Http\Controllers\Admin\ReportController::class, 'data'])->name('.data');
            Route::get('/details', [\App\Http\Controllers\Admin\ReportController::class, 'details'])->name('.details');
            Route::get('/pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('.pdf');
        });

        // Stock
        Route::get('stock', StockManagementController::class)->name('stock.management');

        // Static stock routes must come BEFORE parameterized stock/{product}
        Route::get('stock/search', StockSearchController::class)->name('stock.search');
        Route::get('stock/filter', StockFilterController::class)->name('stock.filter');
        // Stock report PDF (keep for backward compat with generated PDFs)
        Route::get('stock/report/pdf', [StockReportController::class, 'exportPdf'])->name('stock.report.pdf');
        Route::get('stock/report/pdf/{filename}', [StockReportController::class, 'viewPdf'])->name('stock.report.view');

        // Stock In (staff cannot access)
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('stock/in', [StockInController::class, 'index'])->name('stock.in');
            Route::post('stock/in/preview', [StockInController::class, 'preview'])->middleware('throttle:30,1')->name('stock.in.preview');
            Route::post('stock/in/confirm', [StockInController::class, 'confirm'])->middleware('throttle:20,1')->name('stock.in.confirm');
        });

        // Stock Out (staff cannot access)
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('stock/out', [StockOutController::class, 'index'])->name('stock.out');
            Route::post('stock/out/preview', [StockOutController::class, 'preview'])->middleware('throttle:30,1')->name('stock.out.preview');
            Route::post('stock/out/confirm', [StockOutController::class, 'confirm'])->middleware('throttle:20,1')->name('stock.out.confirm');
        });

        // Parameterized stock routes must come AFTER all static routes
        Route::get('stock/{product}', [StockManagementController::class, 'show'])->name('stock.management.show');
        Route::get('stock/{product}/transactions', [StockManagementController::class, 'transactions'])->name('stock.management.transactions');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/trash', [OrderController::class, 'trash'])->name('orders.trash');
        Route::post('orders/{order}/restore', [OrderController::class, 'restore'])->name('orders.restore');
        Route::get('orders/create', [OrderCreateController::class, 'create'])->name('orders.create');
        Route::post('orders', [OrderCreateController::class, 'store'])->name('orders.store');

        // Order Drafts (must come before parameterized orders/{order})
        Route::get('orders/drafts', [OrderDraftController::class, 'index'])->name('order-drafts.index');
        Route::post('orders/drafts', [OrderDraftController::class, 'store'])->name('order-drafts.store');
        Route::get('orders/drafts/{orderDraft}', [OrderDraftController::class, 'show'])->name('order-drafts.show');
        Route::delete('orders/drafts/{orderDraft}', [OrderDraftController::class, 'destroy'])->name('order-drafts.destroy');

        Route::get('orders/product-stock/{product}', [OrderStockController::class, 'productStock'])->name('orders.product-stock');
        Route::post('orders/check-stock', [OrderStatusController::class, 'checkStockAuto'])->name('orders.check-stock');
        Route::get('orders/{order}/edit', [OrderEditController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderEditController::class, 'update'])->name('orders.update');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/update-status', [OrderStatusController::class, 'updateStatus'])->name('orders.update-status');
        Route::delete('orders/{order}', [OrderDeleteController::class, 'destroy'])->name('orders.destroy');
        Route::delete('orders/{order}/force-delete', [OrderDeleteController::class, 'forceDestroy'])->name('orders.force-delete');

        // Product
        Route::middleware('role:superadmin,admin')->group(function () {
            Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
            Route::post('products', [ProductController::class, 'store'])->name('stock.products.store');
            Route::put('products/{product}', [ProductController::class, 'update'])->name('stock.products.update');
        });

        Route::middleware('role:superadmin')->group(function () {
            Route::get('users', [WorkerController::class, 'index'])->name('workers.index');
            Route::get('users/create', [WorkerController::class, 'create'])->name('workers.create');
            Route::post('users', [WorkerController::class, 'store'])->name('workers.store');
            Route::get('users/{worker}/edit', [WorkerController::class, 'edit'])->name('workers.edit');
            Route::put('users/{worker}', [WorkerController::class, 'update'])->name('workers.update');
            Route::post('users/{worker}/toggle-status', [WorkerController::class, 'toggleStatus'])->name('workers.toggle-status');
            Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
            Route::post('monitoring/traps/{trap}/release', function (\App\Models\LoginTrap $trap) {
                $trap->release();
                return back()->with('success', 'Trap released successfully.');
            })->name('monitoring.traps.release');
            Route::get('inquiries', [InquiryController::class, 'index'])->name('inquiries.index');
            Route::get('inquiries/{inquiry}', [InquiryController::class, 'show'])->name('inquiries.show');
            Route::delete('inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
        });

    });
});

require __DIR__.'/finance.php';

require __DIR__.'/website.php';

require __DIR__.'/seo.php';

require __DIR__.'/tracking.php';

require __DIR__.'/shop.php';

// Tracking CAPI bridge (no auth — fire-and-forget from client)
Route::post('/__tracking/capi', [\App\Http\Controllers\Tracking\CapiBridgeController::class, '__invoke'])
    ->middleware('throttle:30,1');

// Trap catch-all — must be the LAST route
Route::get('/{any}', function () {
    if (session()->has('trap_session')) {
        return view('trap.xxxxx');
    }
    return redirect()->route('authentication');
})->where('any', '.*')->middleware('web');
