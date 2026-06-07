<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
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
        WebsiteCategory::create($validated);

        return redirect(admin_route('website.categories'))->with('success', 'Category created.');
    }

    public function update(Request $request, ?string $role = null, WebsiteCategory $category)
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

        return redirect(admin_route('website.categories'))->with('success', 'Category updated.');
    }

    public function destroy(?string $role = null, WebsiteCategory $category)
    {
        if ($category->projects()->exists()) {
            return redirect(admin_route('website.categories'))
                ->with('error', 'Cannot delete category with existing projects.');
        }
        $category->children()->delete();
        $category->delete();

        return redirect(admin_route('website.categories'))->with('success', 'Category deleted.');
    }
}
