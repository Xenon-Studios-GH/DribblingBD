<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\WebsiteProject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        $type = $request->get('type', 'all');
        match ($type) {
            'player' => $query->whereRaw('LOWER(product_name) LIKE ?', ['%player edition%']),
            default => null,
        };

        $stock = $request->get('stock', 'all');
        match ($stock) {
            'in' => $query->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0)),
            'out' => $query->whereDoesntHave('stocks', fn($q) => $q->where('quantity', '>', 0)),
            default => null,
        };

        $sort = $request->get('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default => $query->latest(),
        };

        $products = $query->with(['project', 'stocks'])->paginate(12)->withQueryString();

        return view('shop.products.index', compact('products', 'sort', 'type', 'stock') + ['seoPage' => 'shop']);
    }

    public function show(WebsiteProject $project)
    {
        \Illuminate\Support\Facades\Log::info('Attempting to show project: ' . $project->id);
        $product = $project->product;

        if (!$product || !$product->is_active || !$project->is_active) {
            \Illuminate\Support\Facades\Log::warning('Product/Project not active or missing for project ID: ' . $project->id);
            abort(404);
        }

        $product->loadMissing(['stocks', 'project.images', 'project.category']);

        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $stockMap = collect($allSizes)->mapWithKeys(fn ($s) => [
            $s => $product->stocks->firstWhere('size', $s)?->quantity ?? 0,
        ])->toArray();
        $firstAvailable = collect($allSizes)->first(fn ($s) => ($stockMap[$s] ?? 0) > 0, 'M');

        $related = Product::with('project')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        $productFormData = [
            'firstAvailable' => $firstAvailable,
            'stockMap' => $stockMap,
            'productName' => $product->product_name,
            'productCode' => $product->product_code,
            'sizes' => $allSizes,
        ];

        return view('shop.products.show', compact('product', 'related', 'productFormData') + ['seoable' => $product, 'seoPage' => null]);
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $safeQ = str_replace(['%', '_'], ['\\%', '\\_'], $q);
        $products = Product::with(['project.images'])
            ->where('is_active', true)
            ->where(function ($query) use ($safeQ) {
                $query->where('product_name', 'like', "%{$safeQ}%")
                  ->orWhere('product_code', 'like', "%{$safeQ}%");
            })
            ->take(8)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->product_name,
                'code' => $p->product_code,
                'price' => (int) $p->price,
                'url' => $p->project ? route('shop.products.show', $p->project) : '#',
                'image' => ($p->project && $p->project->images->isNotEmpty()) ? storage_url($p->project->images->first()->image_path) : null,
            ]);

        return response()->json($products);
    }
}
