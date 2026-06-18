<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;
use App\Services\Website\ImageService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    protected ImageService $imageService;
    protected WorkLogService $workLogService;

    public function __construct(ImageService $imageService, WorkLogService $workLogService)
    {
        $this->imageService = $imageService;
        $this->workLogService = $workLogService;
    }

    public function index(Request $request)
    {
        $query = Product::query()
            ->select('products.*')
            ->leftJoin('website_projects', 'products.id', '=', 'website_projects.product_id')
            ->with('project.images');

        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                  ->orWhere('products.product_code', 'like', "%{$search}%");
            });
        }

        $status = $request->get('status');
        if ($status === 'complete') {
            $query->whereNotNull('website_projects.id')->where('website_projects.is_active', true);
        } elseif ($status === 'pending') {
            $query->whereNull('website_projects.id');
        }

        $categoryId = $request->get('category_id');
        if ($categoryId) {
            $query->where('website_projects.category_id', $categoryId);
        }

        $products = $query->orderBy('products.created_at', 'desc')->paginate(20);

        if ($request->ajax()) {
            return view('website.projects._table', compact('products'));
        }

        $categories = WebsiteCategory::with('parent')->orderBy('name')->get();

        return view('website.projects.index', compact('products', 'categories'));
    }

    public function createFromProduct(Product $product)
    {
        if ($product->project) {
            return redirect(admin_route('website.projects.edit', $product->project));
        }

        $project = WebsiteProject::create([
            'product_id' => $product->id,
            'regular_price' => $product->price,
            'slug' => Str::slug($product->product_name) . '-' . $product->id,
            'is_active' => false,
            'created_by' => Auth::id(),
        ]);

        $this->workLogService->log('Project Created', 'website', $project->id, "Website project for '{$product->product_name}' created");

        return redirect(admin_route('website.projects.edit', $project));
    }

    public function edit(WebsiteProject $project)
    {
        $project->load(['images', 'product']);
        $categories = WebsiteCategory::with('parent')->orderBy('name')->get();
        return view('website.projects.form', compact('project', 'categories'));
    }

    public function update(Request $request, WebsiteProject $project)
    {
        $validated = $request->validate([
            'regular_price' => 'required|numeric|min:0',
            'offer_price' => 'nullable|numeric|min:0',
            'category_id' => 'nullable|exists:website_categories,id',
            'details' => 'nullable|string',
            'slug' => 'required|string|max:160|unique:website_projects,slug,' . $project->id,
            'is_active' => 'boolean',
        ]);

        $validated['updated_by'] = Auth::id();

        $removeImages = $request->input('remove_images', []);
        $slots = $request->file('images', []);

        $hasExistingImages = $project->images()->exists();
        $hasNewUploads = collect($slots)->contains(fn ($f) => $f instanceof \Illuminate\Http\UploadedFile);

        if (!$hasExistingImages || $hasNewUploads || !empty($removeImages)) {
            $this->imageService->validateImages($slots);
        }

        $project->update($validated);

        $this->imageService->syncImages($project, $slots, $removeImages);

        $this->workLogService->log('Project Updated', 'website', $project->id, "Website project '{$project->product?->product_name}' updated");

        return redirect(admin_route('website.projects'))->with('success', 'Product updated.');
    }

    public function toggleActive(WebsiteProject $project)
    {
        $project->update(['is_active' => !$project->is_active]);

        $status = $project->is_active ? 'activated' : 'deactivated';
        $this->workLogService->log('Project ' . ucfirst($status), 'website', $project->id, "Website project '{$project->product?->product_name}' {$status}");
        return back()->with('success', "Product {$status} successfully.");
    }

    public function toggleProductActive(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $status = $product->is_active ? 'activated' : 'deactivated';
        $this->workLogService->log('Product ' . ucfirst($status), 'stock', $product->id, "Product '{$product->product_name}' {$status}");
        return back()->with('success', "Product {$status} successfully.");
    }
}
