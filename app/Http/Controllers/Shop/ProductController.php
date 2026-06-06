<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        $type = $request->get('type', 'all');
        match ($type) {
            'player' => $query->whereRaw('LOWER(product_name) LIKE ?', ['%player edition%']),
            'fan' => $query->whereRaw('LOWER(product_name) LIKE ?', ['%fan%']),
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

        $products = $query->with('project')->paginate(12)->withQueryString();

        return view('shop.products.index', compact('products', 'sort', 'type', 'stock'));
    }

    public function show(Product $product, ?string $slug = null)
    {
        $expected = $product->project?->slug ?? Str::slug($product->product_name);
        if ($slug !== $expected) {
            return redirect()->route('shop.products.show', [$product->product_code, $expected]);
        }

        $product->load(['stocks', 'project.images', 'project.category']);

        $allSizes = ['S', 'M', 'L', 'XL', 'XXL'];
        $stockMap = collect($allSizes)->mapWithKeys(fn ($s) => [
            $s => $product->stocks->firstWhere('size', $s)?->quantity ?? 0,
        ])->toArray();
        $firstAvailable = collect($allSizes)->first(fn ($s) => ($stockMap[$s] ?? 0) > 0, 'M');

        $related = Product::with('project')
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

        return view('shop.products.show', compact('product', 'related', 'productFormData'));
    }

    public function search(Request $request): JsonResponse
    {
        $q = $request->get('q', '');

        if (mb_strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::where('product_name', 'like', "%{$q}%")
            ->orWhere('product_code', 'like', "%{$q}%")
            ->take(8)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->product_name,
                'code' => $p->product_code,
                'price' => (int) $p->price,
                'url' => route('shop.products.show', [$p->product_code, $p->project?->slug ?? Str::slug($p->product_name)]),
            ]);

        return response()->json($products);
    }
}
