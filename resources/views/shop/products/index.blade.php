@php
$headings = ['all' => 'All Jerseys', 'player' => 'Player Edition', 'fan' => 'Fan Edition'];
$title = $headings[$type ?? 'all'] ?? 'All Jerseys';
@endphp

@extends('shop.layouts.shop', ['title' => "Shop $title"])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">{{ $title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Showing {{ $products->firstItem() }}-{{ $products->lastItem() }} of {{ $products->total() }} products</p>
        </div>

    </div>

    {{-- Content with sidebar --}}
    <div class="flex gap-8">
        {{-- Filter sidebar --}}
        <aside class="hidden lg:block w-52 shrink-0">
            <div class="sticky top-24 space-y-6">
                {{-- Category --}}
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Category</h3>
                    <div class="space-y-0.5">
                        @php
                        $types = [
                        'all' => 'All Jerseys',
                        'player' => 'Player Edition',
                        'fan' => 'Fan Edition',
                        ];
                        @endphp
                        @foreach ($types as $val => $label)
                        @php
                        $url = route('shop.products.index', array_filter(['type' => $val === 'all' ? null : $val, 'stock' => $stock !== 'all' ? $stock : null, 'sort' => $sort !== 'newest' ? $sort : null]));
                        @endphp
                        <a href="{{ $url }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                          {{ $type === $val ? 'bg-[#E85D2C]/10 text-[#E85D2C]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $type === $val ? 'bg-[#E85D2C]' : 'bg-gray-300' }}"></span>
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Availability --}}
                <div>
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Availability</h3>
                    <div class="space-y-0.5">
                        @php
                        $stocks = [
                        'all' => 'All',
                        'in' => 'In Stock',
                        'out' => 'Out of Stock',
                        ];
                        @endphp
                        @foreach ($stocks as $val => $label)
                        @php
                        $url = route('shop.products.index', array_filter(['stock' => $val === 'all' ? null : $val, 'type' => $type !== 'all' ? $type : null, 'sort' => $sort !== 'newest' ? $sort : null]));
                        @endphp
                        <a href="{{ $url }}"
                            class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                          {{ $stock === $val ? 'bg-[#E85D2C]/10 text-[#E85D2C]' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            @if ($val === 'in')
                            <i class="fas fa-check-circle w-3.5 h-3.5"></i>
                            @elseif ($val === 'out')
                            <i class="fas fa-times-circle w-3.5 h-3.5"></i>
                            @else
                            <span class="w-1.5 h-1.5 rounded-full {{ $stock === 'all' ? 'bg-[#E85D2C]' : 'bg-gray-300' }}"></span>
                            @endif
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        {{-- Products grid --}}
        <div class="flex-1 min-w-0">
            @if ($products->count() > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($products as $product)
                @php $firstImage = $product->project?->images->first(); @endphp
                <div class="group rounded-2xl bg-white border border-gray-200 hover:border-[#E85D2C]/30 hover:shadow-lg hover:shadow-orange-500/5 transition-all duration-300 overflow-hidden" x-data="{ added: false }">
                    <a href="{{ route('shop.products.show', [$product->product_code, $product->slug]) }}" class="block aspect-[4/5] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-[#E85D2C]/10 to-[#F59E0B]/10 group-hover:scale-105 transition-transform duration-500"></div>
                        <div class="absolute top-3 left-3 z-10">
                            <span class="px-2 py-1 rounded-lg text-[10px] font-semibold bg-white/90 text-gray-700">{{ $product->product_code }}</span>
                        </div>
                        @if ($firstImage)
                        <img src="{{ asset('storage/' . $firstImage->image_path) }}" alt="{{ $product->product_name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-box w-16 h-16 text-gray-300 group-hover:text-gray-400 transition-colors"></i>
                        </div>
                        @endif
                        <div class="absolute top-3 right-3 z-10">
                            <button @click.stop='toggleWishlist({ id: {{ $product->id }}, code: @json($product->product_code), name: @json($product->product_name) })' class="p-1.5 rounded-lg bg-white/80 hover:bg-white shadow-sm">
                                <i class="fas fa-heart w-4 h-4" :class="isInWishlist({{ $product->id }}) ? 'text-red-500' : 'text-gray-400'"></i>
                            </button>
                        </div>
                    </a>
                    <div class="p-3 sm:p-4">
                        <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->product_name }}</h3>
                        <div class="mt-1 flex items-center justify-between">
                            <span class="text-base font-bold text-[#E85D2C]">৳{{ number_format($product->price) }}</span>
                        </div>
                        <button @click='added = true; addToCart({ id: {{ $product->id }}, name: @json($product->product_name), price: {{ $product->price }}, size: "M", quantity: 1 }); setTimeout(() => added = false, 1500)'
                            class="mt-2.5 w-full py-2 rounded-xl text-xs font-semibold transition-all duration-300"
                            :class="added ? 'bg-green-500 text-white' : 'bg-gray-900 text-white hover:bg-gray-800'">
                            <span x-show="!added">Add to Cart</span>
                            <span x-show="added" x-cloak>✓ Added</span>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-10">
                {{ $products->links() }}
            </div>
            @else
            <div class="text-center py-20">
                <i class="fas fa-box w-16 h-16 mx-auto text-gray-300"></i>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">No products yet</h3>
                <p class="mt-1 text-sm text-gray-500">Jerseys will appear here once added to inventory.</p>
                <a href="{{ route('shop.home') }}" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C] hover:text-[#d14d1f]">
                    Back to Home
                    <i class="fas fa-arrow-left w-4 h-4"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@include('shop.components.features')
@endsection