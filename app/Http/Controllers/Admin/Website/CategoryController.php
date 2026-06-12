<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCategory;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $categories = WebsiteCategory::with('parent', 'children')
            ->withCount('projects')
            ->orderBy('name')
            ->get();
        return view('website.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:website_categories,slug',
            'parent_id' => 'nullable|exists:website_categories,id',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = Auth::id();
        $category = WebsiteCategory::create($validated);

        $this->workLogService->log('Category Created', 'website', $category->id, "Website category '{$category->name}' created");

        return redirect(admin_route('website.categories'))->with('success', 'Category created.');
    }

    public function update(Request $request, string $role, WebsiteCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120|unique:website_categories,slug,' . $category->id,
            'parent_id' => 'nullable|exists:website_categories,id|not_in:' . $category->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();
        $category->update($validated);

        $this->workLogService->log('Category Updated', 'website', $category->id, "Website category '{$category->name}' updated");

        return redirect(admin_route('website.categories'))->with('success', 'Category updated.');
    }

    public function destroy(string $role, WebsiteCategory $category)
    {
        if ($category->projects()->exists()) {
            return redirect(admin_route('website.categories'))
                ->with('error', 'Cannot delete category with existing projects.');
        }

        $childrenWithProjects = WebsiteCategory::whereIn('id', $category->children()->pluck('id'))
            ->whereHas('projects')->count();

        if ($childrenWithProjects > 0) {
            $category->children()->each(fn($child) => $child->projects()->update(['category_id' => null]));
        }

        $name = $category->name;
        $category->children()->delete();
        $category->delete();

        $this->workLogService->log('Category Deleted', 'website', $category->id, "Website category '{$name}' deleted");

        return redirect(admin_route('website.categories'))->with('success', 'Category deleted.');
    }
}
