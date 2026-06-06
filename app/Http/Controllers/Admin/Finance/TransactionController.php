<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\FinanceTransactionVersion;
use App\Services\Finance\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function index(Request $request)
    {
        $query = FinanceTransaction::with(['category', 'creator']);

        // Default: last 1 year
        $query->where('date', '>=', $request->date_from ?: now()->subYear());
        $query->where('date', '<=', $request->date_to ?: now());

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        $transactions = $query->latest('date')->paginate(20);
        $categories = FinanceCategory::active()->get();

        return view('finance.transactions.index', compact('transactions', 'categories'));
    }

    public function create()
    {
        $categories = FinanceCategory::active()->get();
        return view('finance.transactions.form', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'nullable|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'date' => 'required|date',
        ]);

        $validated['created_by'] = Auth::id();

        $transaction = FinanceTransaction::create($validated);

        $this->notifications->notifyAdmins(
            'transaction.created',
            'New Transaction',
            Auth::user()->name . ' created a ' . $transaction->type . ' of ৳' . number_format($transaction->amount),
            'transaction',
            $transaction->id
        );

        return redirect(admin_route('finance.transactions'))->with('success', 'Transaction created.');
    }

    public function edit(?string $role = null, FinanceTransaction $transaction)
    {
        $categories = FinanceCategory::active()->get();
        return view('finance.transactions.form', compact('transaction', 'categories'));
    }

    public function update(Request $request, ?string $role = null, FinanceTransaction $transaction)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'category_id' => 'nullable|exists:finance_categories,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:1000',
            'date' => 'required|date',
        ]);

        $oldData = $transaction->only(['type', 'category_id', 'amount', 'description', 'date']);

        $validated['updated_by'] = Auth::id();
        $transaction->update($validated);

        FinanceTransactionVersion::create([
            'transaction_id' => $transaction->id,
            'old_data' => $oldData,
            'new_data' => $transaction->only(['type', 'category_id', 'amount', 'description', 'date']),
            'edited_by' => Auth::id(),
        ]);

        $this->notifications->notifyAdmins(
            'transaction.updated',
            'Transaction Updated',
            Auth::user()->name . ' updated a transaction of ৳' . number_format($transaction->amount),
            'transaction',
            $transaction->id
        );

        return redirect(admin_route('finance.transactions'))->with('success', 'Transaction updated.');
    }

    public function destroy(?string $role = null, FinanceTransaction $transaction)
    {
        $transaction->delete();

        $this->notifications->notifyAdmins(
            'transaction.deleted',
            'Transaction Deleted',
            Auth::user()->name . ' deleted a transaction',
            'transaction',
            $transaction->id
        );

        return redirect(admin_route('finance.transactions'))->with('success', 'Transaction deleted.');
    }
}
