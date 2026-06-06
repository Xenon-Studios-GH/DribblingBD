@extends('shop.layouts.shop', ['title' => $product->product_name])

@push('styles')
<style>
    .gallery-main {
        border-radius: 1rem;
        overflow: hidden;
        background: #f3f4f6;
        aspect-ratio: 4/5;
        position: relative;
    }
    .gallery-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: opacity 0.3s ease;
    }
    .gallery-thumbs {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }
    .gallery-thumbs button {
        flex: 1;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid transparent;
        transition: border-color 0.2s ease;
        padding: 0;
        cursor: pointer;
        background: #f3f4f6;
    }
    .gallery-thumbs button.active {
        border-color: #E85D2C;
    }
    .gallery-thumbs img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
</style>
@endpush

@section('content')
@php
$project = $product->project;
$images = $project?->images ?? collect();
$regularPrice = $project?->regular_price ?? $product->price;
$offerPrice = $project?->offer_price;
$hasOffer = $offerPrice && $offerPrice < $regularPrice;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="/" class="hover:text-[#E85D2C]">Home</a>
        @if ($project?->category)
        @php $cat = $project->category; @endphp
        @if ($cat->parent)
        <i class="fas fa-chevron-right w-3 h-3"></i>
        <a href="{{ route('shop.category', $cat->parent->slug) }}" class="hover:text-[#E85D2C]">{{ $cat->parent->name }}</a>
        @endif
        <i class="fas fa-chevron-right w-3 h-3"></i>
        <a href="{{ route('shop.category', $cat->slug) }}" class="hover:text-[#E85D2C]">{{ $cat->name }}</a>
        @endif
        <i class="fas fa-chevron-right w-3 h-3"></i>
        <a href="{{ route('shop.products.index') }}" class="hover:text-[#E85D2C]">Shop</a>
        <i class="fas fa-chevron-right w-3 h-3"></i>
        <span class="text-gray-900 font-medium truncate">{{ $product->product_name }}</span>
    </nav>

    <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            {{-- Image gallery --}}
            @if ($images->count() >= 1)
            <div x-data="galleryFlip()" class="w-full">
                <div class="gallery-main shadow-xl">
                    <template x-for="(img, i) in images" :key="i">
                        <img :src="'/storage/' + img" 
                             x-show="activeIndex === i"
                             class="absolute inset-0">
                    </template>
                    <div class="absolute top-3 left-3 z-10 pointer-events-none">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/90 text-gray-800">{{ $product->product_code }}</span>
                    </div>
                    @if ($hasOffer)
                    <div class="absolute top-3 right-3 z-10 pointer-events-none">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-500 text-white">Sale</span>
                    </div>
                    @endif
                </div>
                @if ($images->count() >= 2)
                <div class="gallery-thumbs">
                    @foreach ($images as $i => $img)
                    <button @click="activeIndex = {{ $i }}" 
                            :class="{ 'active': activeIndex === {{ $i }} }">
                        <img src="{{ asset('storage/' . $img->image_path) }}" alt="">
                    </button>
                    @endforeach
                </div>
                @endif
            </div>
            @elseif ($images->isNotEmpty())
            <div class="rounded-2xl overflow-hidden shadow-xl bg-gray-100">
                <div class="aspect-[4/5] relative">
                    <img src="{{ asset('storage/' . $images[0]->image_path) }}" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute top-3 left-3 z-10">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/90 text-gray-800">{{ $product->product_code }}</span>
                    </div>
                    @if ($hasOffer)
                    <div class="absolute top-3 right-3 z-10">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-green-500 text-white">Sale</span>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="aspect-[4/5] rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center shadow-xl">
                <i class="fas fa-box w-24 h-24 text-gray-300"></i>
            </div>
            @endif

        {{-- Product info --}}
        <div class="flex flex-col">
            <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">
                {{ $project?->category?->name ?? 'Premium Jersey' }}
            </span>
            <h1 class="mt-2 text-2xl lg:text-3xl font-bold text-gray-900">{{ $product->product_name }}</h1>

            <div class="mt-4 flex items-baseline gap-2">
                @if ($hasOffer)
                <span class="text-3xl font-bold text-[#E85D2C]">৳{{ number_format($offerPrice) }}</span>
                <span class="text-lg text-gray-400 line-through">৳{{ number_format($regularPrice) }}</span>
                @else
                <span class="text-3xl font-bold text-[#E85D2C]">৳{{ number_format($regularPrice) }}</span>
                @endif
            </div>

            @if ($project?->details)
            <div class="mt-4 text-sm text-gray-600 leading-relaxed prose prose-sm max-w-none">
                {!! nl2br(e($project->details)) !!}
            </div>
            @else
            <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                Premium quality {{ $product->product_name }} jersey. Designed for comfort and performance on the pitch. Features breathable fabric, reinforced stitching, and authentic detailing. Perfect for match day or casual wear.
            </p>
            @endif

            @include('shop.components.product-form')

            {{-- Additional info --}}
            <div class="mt-8 pt-6 border-t border-gray-200 space-y-3">
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <i class="fas fa-check w-4 h-4 text-green-500"></i>
                    @php
                    $totalStock = $product->stocks->sum('quantity');
                    @endphp
                    @if ($totalStock > 0)
                    In Stock
                    @else
                    <span class="text-red-500">Out of Stock</span>
                    @endif
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <i class="fas fa-shopping-bag w-4 h-4 text-green-500"></i>
                    Free shipping on orders over ৳3,000
                </div>
                <div class="flex items-center gap-3 text-sm text-gray-600">
                    <i class="fas fa-sync-alt w-4 h-4 text-green-500"></i>
                    72 Hours Home Delivery
                </div>
            </div>
        </div>
    </div>

    {{-- Related Products --}}
    @if ($related->count() > 0)
    <section class="mt-16 pt-12 border-t border-gray-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">You May Also Like</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach ($related as $rel)
            @php $relImage = $rel->project?->images->first(); @endphp
            <div class="group rounded-2xl bg-white border border-gray-200 hover:border-[#E85D2C]/30 hover:shadow-lg transition-all duration-300 overflow-hidden" x-data="{ added: false }">
                <a href="{{ route('shop.products.show', [$rel->product_code, $rel->slug]) }}" class="block aspect-[4/5] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-[#E85D2C]/10 to-[#F59E0B]/10 group-hover:scale-105 transition-transform duration-500"></div>
                    @if ($relImage)
                    <img src="{{ asset('storage/' . $relImage->image_path) }}" alt="{{ $rel->product_name }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-box w-12 h-12 text-gray-300"></i>
                    </div>
                    @endif
                </a>
                <div class="p-3">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $rel->product_name }}</h3>
                    <span class="text-sm font-bold text-[#E85D2C]">৳{{ number_format($rel->price) }}</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    </div>

    <script>
        function galleryFlip() {
            return {
                activeIndex: 0,
                images: @json($images->pluck('image_path')),
            };
        }
    </script>
    @endSection