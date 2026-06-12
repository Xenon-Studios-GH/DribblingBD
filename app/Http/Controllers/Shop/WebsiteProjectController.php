<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;

class WebsiteProjectController extends Controller
{
    public function index()
    {
        $projects = WebsiteProject::active()
            ->with(['product', 'category.parent', 'images'])
            ->whereHas('product', fn($q) => $q->active())
            ->whereHas('images')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = WebsiteCategory::with('parent')
            ->whereHas('projects', fn($q) => $q->active()->whereHas('product', fn($q) => $q->active()))
            ->orderBy('name')
            ->get();

        return view('shop.projects.index', compact('projects', 'categories'));
    }

    public function show($categorySlug, $subcategorySlug, $projectSlug)
    {
        $project = WebsiteProject::active()
            ->with(['product', 'category.parent', 'images' => fn($q) => $q->orderBy('sort_order')])
            ->whereHas('product', fn($q) => $q->active())
            ->where('slug', $projectSlug)
            ->firstOrFail();

        $cat = $project->category;
        if (!$cat) {
            abort(404);
        }

        $expectedCategorySlug = $cat->parent?->slug ?? $cat->slug;
        $expectedSubcategorySlug = $cat->slug;

        if ($categorySlug !== $expectedCategorySlug || $subcategorySlug !== $expectedSubcategorySlug) {
            return redirect()->route('shop.project.detail', [
                'categorySlug' => $expectedCategorySlug,
                'subcategorySlug' => $expectedSubcategorySlug,
                'projectSlug' => $project->slug,
            ], 301);
        }

        $relatedProjects = WebsiteProject::active()
            ->where('category_id', $project->category_id)
            ->where('id', '!=', $project->id)
            ->whereHas('product', fn($q) => $q->active())
            ->with(['images', 'product'])
            ->take(4)
            ->get();

        return view('shop.projects.show', compact('project', 'relatedProjects'));
    }

    public function category($categorySlug)
    {
        $category = WebsiteCategory::with('parent')
            ->where('slug', $categorySlug)
            ->firstOrFail();

        $categoryIds = collect([$category->id])->merge(
            $category->children->pluck('id')
        );

        $projects = WebsiteProject::active()
            ->with(['product', 'category.parent', 'images'])
            ->whereIn('category_id', $categoryIds)
            ->whereHas('images')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $categories = WebsiteCategory::with('parent')
            ->whereHas('projects', fn($q) => $q->active())
            ->orderBy('name')
            ->get();

        return view('shop.projects.index', compact('projects', 'categories', 'category'));
    }
}
