@extends('shop.layouts.shop', ['title' => $product->product_name])

@section('content')
    @php
        $colors = ['from-[#E85D2C] to-[#F59E0B]', 'from-blue-600 to-blue-800', 'from-green-600 to-green-800', 'from-red-600 to-red-800', 'from-purple-600 to-purple-800'];
        $color = $colors[$product->id % count($colors)];
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-[#E85D2C]">Home</a>
            <i class="fas fa-chevron-right w-3 h-3"></i>
            <a href="{{ route('shop.products.index') }}" class="hover:text-[#E85D2C]">Shop</a>
            <i class="fas fa-chevron-right w-3 h-3"></i>
            <span class="text-gray-900 font-medium truncate">{{ $product->product_name }}</span>
        </nav>

        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12">
            {{-- Image gallery --}}
            <div>
                <div class="aspect-[4/5] rounded-2xl bg-gradient-to-br {{ $color }} relative overflow-hidden shadow-xl">
                    <div class="absolute inset-0 opacity-15" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 24px 24px;"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-box w-24 h-24 text-white/60"></i>
                    </div>
                    <div class="absolute top-4 left-4">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-white/90 text-gray-800">{{ $product->product_code }}</span>
                    </div>
                </div>
                <div class="grid grid-cols-4 gap-3 mt-3">
                    @foreach (['from-gray-200 to-gray-300', 'from-gray-100 to-gray-200', 'from-gray-100 to-gray-200', 'from-gray-100 to-gray-200'] as $i => $thumbColor)
                        <div class="aspect-[4/5] rounded-xl bg-gradient-to-br {{ $thumbColor }} {{ $i === 0 ? 'ring-2 ring-[#E85D2C]' : '' }}"></div>
                    @endforeach
                </div>
            </div>

            {{-- Product info --}}
            <div class="flex flex-col">
                <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">Premium Jersey</span>
                <h1 class="mt-2 text-2xl lg:text-3xl font-bold text-gray-900">{{ $product->product_name }}</h1>

                <div class="mt-3 flex items-center gap-2">
                    <div class="flex items-center gap-0.5">
                        @for ($i = 0; $i < 5; $i++)
                            <i class="fas fa-star w-4 h-4 {{ $i < 4 ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <span class="text-sm text-gray-500">(24 reviews)</span>
                </div>

                <div class="mt-4 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-[#E85D2C]">৳{{ number_format($product->price) }}</span>
                    @if ($product->price > 1500)
                        <span class="text-lg text-gray-400 line-through">৳{{ number_format($product->price + 500) }}</span>
                        <span class="px-2 py-0.5 rounded text-xs font-semibold bg-green-100 text-green-700">Save ৳500</span>
                    @endif
                </div>

                <p class="mt-4 text-sm text-gray-600 leading-relaxed">
                    Premium quality {{ $product->product_name }} jersey. Designed for comfort and performance on the pitch. Features breathable fabric, reinforced stitching, and authentic detailing. Perfect for match day or casual wear.
                </p>

                @include('shop.components.product-form')

                {{-- Additional info --}}
                <div class="mt-8 pt-6 border-t border-gray-200 space-y-3">
                    <div class="flex items-center gap-3 text-sm text-gray-600">
                        <i class="fas fa-check w-4 h-4 text-green-500"></i>
                        In Stock
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
                        <div class="group rounded-2xl bg-white border border-gray-200 hover:border-[#E85D2C]/30 hover:shadow-lg transition-all duration-300 overflow-hidden" x-data="{ added: false }">
                            <a href="{{ route('shop.products.show', [$rel->product_code, $rel->slug]) }}" class="block aspect-[4/5] bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-br from-[#E85D2C]/10 to-[#F59E0B]/10 group-hover:scale-105 transition-transform duration-500"></div>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <i class="fas fa-box w-12 h-12 text-gray-300"></i>
                                </div>
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
@endsection
