{{-- Size + Quantity + Actions (shared Alpine scope) --}}
<div x-data='productForm(@json($productFormData, JSON_HEX_TAG))'>
    {{-- Size selector --}}
    <div class="mt-6">
        <label class="text-sm font-semibold text-gray-900">Size</label>
        <div class="flex flex-wrap gap-2 mt-2">
            @foreach ($productFormData['sizes'] as $size)
                @php
                    $stock = $product->stocks->firstWhere('size', $size);
                @endphp
                <button @click='setSize(@json($size, JSON_HEX_TAG))' :class="selectedSize === '{{ $size }}' ? 'bg-[#E85D2C] text-white border-[#E85D2C]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#E85D2C]'" class="px-4 py-2.5 rounded-xl border text-sm font-medium transition-colors">
                    {{ $size }}
                </button>
            @endforeach
        </div>
        <p class="mt-1.5 text-xs text-gray-500">
            <span x-show="stockQty > 0" x-cloak><span x-text="stockQty"></span> available</span>
            <span x-show="stockQty < 1" x-cloak class="text-red-500">No stock for this size</span>
        </p>
    </div>

    {{-- Quantity --}}
    <div class="mt-4">
        <label class="text-sm font-semibold text-gray-900">Quantity</label>
        <div class="flex items-center gap-3 mt-2">
            <button @click="qty = Math.max(1, qty - 1)" :disabled="qty < 2" aria-label="Decrease quantity" class="w-11 h-11 rounded-xl border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <i class="fas fa-minus w-4 h-4"></i>
            </button>
            <span class="w-12 text-center text-lg font-semibold text-gray-900" x-text="qty"></span>
            <button @click="qty = Math.min(100, qty + 1)" :disabled="qty >= 100" aria-label="Increase quantity" class="w-11 h-11 rounded-xl border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-50 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                <i class="fas fa-plus w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Actions --}}
    <div class="mt-6 flex flex-col sm:flex-row gap-3">
        <div x-show="stockQty > 0 && qty <= stockQty" x-cloak class="flex-1">
            <button @click='addToCart({ id: {{ $product->id }}, name: @json($product->product_name, JSON_HEX_TAG), price: {{ $product->price }}, size: selectedSize, quantity: qty, image: @json($product->project?->images->first()?->image_path ? 'storage/' . $product->project->images->first()->image_path : '', JSON_HEX_TAG), code: @json($product->product_code, JSON_HEX_TAG), slug: @json($product->slug, JSON_HEX_TAG) })' class="w-full px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors shadow-lg shadow-[#E85D2C]/20">
                Add to Cart
            </button>
        </div>
        <div x-show="stockQty < 1 || qty > stockQty" x-cloak class="flex-1">
            <a :href="whatsappUrl" class="w-full px-6 py-3 rounded-xl bg-yellow-500 text-white font-semibold text-sm hover:bg-yellow-600 transition-colors shadow-lg shadow-yellow-500/20 flex items-center justify-center gap-2" target="_blank">
                <i class="fab fa-whatsapp w-5 h-5"></i>
                Contact for Stock
            </a>
        </div>
        <button @click='toggleWishlist({ id: {{ $product->id }}, code: @json($product->product_code, JSON_HEX_TAG), name: @json($product->product_name, JSON_HEX_TAG) })' class="px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold text-sm hover:border-red-300 hover:text-red-500 transition-colors flex items-center justify-center gap-2" aria-label="Add to wishlist">
            <i class="fas fa-heart w-4 h-4" :class="isInWishlist({{ $product->id }}) ? 'text-red-500' : ''"></i>
            <span x-text="isInWishlist({{ $product->id }}) ? 'Saved' : 'Save'"></span>
        </button>
    </div>
</div>