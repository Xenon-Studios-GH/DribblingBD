<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Services\Finance\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected NotificationService $notifications;

    public function __construct(NotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function index()
    {
        $incomeCategories = FinanceCategory::income()->withCount('transactions')->get();
        $expenseCategories = FinanceCategory::expense()->withCount('transactions')->get();
        return view('finance.categories.index', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = Auth::id();
        FinanceCategory::create($validated);

        $this->notifications->notifyAdmins(
            'category.created',
            'New Category',
            Auth::user()->name . ' created a new ' . $validated['type'] . ' category: ' . $validated['name'],
            'category',
            null
        );

        return redirect(admin_route('finance.categories'))->with('success', 'Category created.');
    }

    public function update(Request $request, string $role, FinanceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $category->update($validated);

        return redirect(admin_route('finance.categories'))->with('success', 'Category updated.');
    }

    public function destroy(string $role, FinanceCategory $category)
    {
        $category->transactions()->update(['category_id' => null]);
        $category->delete();
        return redirect(admin_route('finance.categories'))->with('success', 'Category deleted.');
    }
}
