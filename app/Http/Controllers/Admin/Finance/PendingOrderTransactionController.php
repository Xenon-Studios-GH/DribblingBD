<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceTransaction;
use App\Models\PendingOrderTransaction;
use App\Services\Finance\NotificationService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PendingOrderTransactionController extends Controller
{
    protected NotificationService $notifications;
    protected WorkLogService $workLogService;

    public function __construct(NotificationService $notifications, WorkLogService $workLogService)
    {
        $this->notifications = $notifications;
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $pendingOrders = PendingOrderTransaction::where('status', 'pending')
            ->latest()
            ->paginate(20);

        return view('finance.pending-orders.index', compact('pendingOrders'));
    }

    public function confirm(PendingOrderTransaction $pending)
    {
        if ($pending->status !== 'pending') {
            return back()->withErrors(['status' => 'This record has already been processed.']);
        }

        DB::transaction(function () use ($pending) {
            FinanceTransaction::create([
                'type' => 'income',
                'category_id' => 1,
                'amount' => $pending->product_sales_amount,
                'description' => "Product sales from Order #{$pending->order_no} ({$pending->customer_name})",
                'date' => today(),
                'created_by' => Auth::id(),
            ]);

            if ($pending->dtf_sales_amount > 0) {
                FinanceTransaction::create([
                    'type' => 'income',
                    'category_id' => 2,
                    'amount' => $pending->dtf_sales_amount,
                    'description' => "DTF sales from Order #{$pending->order_no} ({$pending->customer_name})",
                    'date' => today(),
                    'created_by' => Auth::id(),
                ]);
            }

            if ($pending->patch_sales_amount > 0) {
                FinanceTransaction::create([
                    'type' => 'income',
                    'category_id' => 3,
                    'amount' => $pending->patch_sales_amount,
                    'description' => "Patch sales from Order #{$pending->order_no} ({$pending->customer_name})",
                    'date' => today(),
                    'created_by' => Auth::id(),
                ]);
            }

            $pending->update(['status' => 'confirmed']);
        });

        $this->notifications->notifyAdmins(
            'order.transaction.confirmed',
            'Order Transaction Confirmed',
            Auth::user()->name . ' confirmed transactions for Order #' . $pending->order_no,
            'transaction',
            $pending->id
        );

        $this->workLogService->log('Pending Order Confirmed', 'finance', $pending->id, "Order #{$pending->order_no} transactions confirmed");

        return redirect(admin_route('finance.pending-orders'))->with('success', "Order #{$pending->order_no} transactions confirmed and added.");
    }

    public function destroy(PendingOrderTransaction $pending)
    {
        $orderNo = $pending->order_no;
        $pending->update(['status' => 'cancelled']);

        $this->workLogService->log('Pending Order Cancelled', 'finance', $pending->id, "Order #{$orderNo} pending transactions cancelled");

        return redirect(admin_route('finance.pending-orders'))->with('success', "Order #{$orderNo} pending transactions cancelled.");
    }
}
