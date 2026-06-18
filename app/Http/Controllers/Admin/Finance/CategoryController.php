<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\FinanceCategory;
use App\Services\Finance\NotificationService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
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
        $category = FinanceCategory::create($validated);

        $this->workLogService->log('Category Created', 'finance', $category->id, "{$category->type->value} category '{$category->name}' created");

        $this->notifications->notifyAdmins(
            'category.created',
            'New Category',
            Auth::user()->name . ' created a new ' . $category->type->value . ' category: ' . $category->name,
            'category',
            null
        );

        return redirect(admin_route('finance.categories'))->with('success', 'Category created.');
    }

    public function update(Request $request, FinanceCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $category->update($validated);

        $this->workLogService->log('Category Updated', 'finance', $category->id, "Category '{$category->name}' updated");

        return redirect(admin_route('finance.categories'))->with('success', 'Category updated.');
    }

    public function destroy(FinanceCategory $category)
    {
        $name = $category->name;
        $category->transactions()->update(['category_id' => null]);
        $category->delete();
        $this->workLogService->log('Category Deleted', 'finance', $category->id, "Category '{$name}' deleted");
        return redirect(admin_route('finance.categories'))->with('success', 'Category deleted.');
    }
}
