@extends('shop.layouts.shop', ['title' => 'Home'])

@section('content')
    {{-- Hero Section --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-orange-50 via-white to-amber-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="max-w-xl">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-[#E85D2C]/10 text-[#E85D2C] mb-5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#E85D2C]"></span>
                        Premium Jersey Store
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight">
                        Your Identity.
                        <span class="text-[#E85D2C]">Your Jersey.</span>
                        Your Game.
                    </h1>
                    <p class="mt-4 text-lg text-gray-600 leading-relaxed">
                        Premium custom jerseys for clubs, tournaments, and champions. Design your look, own the pitch.
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-[#E85D2C] text-white font-semibold text-sm hover:bg-[#d14d1f] transition-colors shadow-lg shadow-[#E85D2C]/20">
                            Shop Ready-Made
                            <i class="fas fa-arrow-right w-4 h-4"></i>
                        </a>
                        <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border-2 border-gray-300 text-gray-700 font-semibold text-sm hover:border-[#E85D2C] hover:text-[#E85D2C] transition-colors">
                            Customize Your Jersey
                        </a>
                    </div>
                    <div class="mt-8 flex items-center gap-6 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check w-4 h-4 text-green-500"></i>
                            Premium Quality
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check w-4 h-4 text-green-500"></i>
                            Free Shipping
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fas fa-check w-4 h-4 text-green-500"></i>
                            72 Hours Home Delivery
                        </span>
                    </div>
                </div>
                <div class="relative flex justify-center lg:justify-end">
                    <div class="w-72 h-80 sm:w-80 sm:h-96 lg:w-96 lg:h-[28rem] rounded-3xl bg-gradient-to-br from-[#E85D2C] via-[#F97316] to-[#F59E0B] flex items-center justify-center shadow-2xl shadow-orange-500/20 relative overflow-hidden">
                        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle at 1px 1px, white 1px, transparent 0); background-size: 20px 20px;"></div>
                        <div class="text-center px-6">
                            <i class="fas fa-box w-20 h-20 mx-auto text-white/80"></i>
                            <p class="mt-4 text-white/90 font-bold text-lg">Premium Jerseys</p>
                            <p class="text-white/60 text-sm">2026 Collection</p>
                            <div class="mt-4 flex justify-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-white/80"></span>
                                <span class="w-2 h-2 rounded-full bg-white/40"></span>
                                <span class="w-2 h-2 rounded-full bg-white/40"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- Wave divider --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" class="w-full h-auto" preserveAspectRatio="none">
                <path d="M0 30Q360 60 720 30T1440 30V60H0V30Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- Categories Strip --}}
    <section class="py-10 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-center gap-4">
                @php
                    $categories = [
                        ['name' => 'All Jerseys', 'slug' => 'all', 'icon' => '👕', 'color' => 'from-[#E85D2C] to-[#F59E0B]'],
                        ['name' => 'Player Edition', 'slug' => 'player', 'icon' => '⭐', 'color' => 'from-blue-600 to-blue-800'],
                        ['name' => 'Fan Edition', 'slug' => 'fan', 'icon' => '🎉', 'color' => 'from-green-600 to-green-800'],
                    ];
                @endphp
                @foreach ($categories as $cat)
                    <a href="{{ route('shop.products.index', $cat['slug'] !== 'all' ? ['type' => $cat['slug']] : []) }}" class="group flex flex-col items-center gap-2 p-4 rounded-2xl bg-gray-50 hover:bg-gradient-to-br {{ $cat['color'] }} hover:text-white transition-all duration-300 min-w-0 flex-1 max-w-48">
                        <span class="text-2xl group-hover:scale-110 transition-transform">{{ $cat['icon'] }}</span>
                        <span class="text-xs font-medium text-gray-700 group-hover:text-white">{{ $cat['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- New Arrivals --}}
    <section class="py-14 lg:py-18 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">Latest</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">New Arrivals</h2>
                </div>
                <a href="{{ route('shop.products.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C] hover:text-[#d14d1f] transition-colors">
                    View All
                    <i class="fas fa-chevron-right w-4 h-4"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($newArrivals as $product)
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
                            <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                                <span class="text-xs font-medium text-white">Quick View</span>
                            </div>
                        </a>
                        <div class="p-3 sm:p-4">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->product_name }}</h3>
                            <div class="mt-1">
                                <span class="text-base font-bold text-[#E85D2C]">৳{{ number_format($product->price) }}</span>
                            </div>
                            <button @click='added = true; addToCart({ id: {{ $product->id }}, name: @json($product->product_name), price: {{ $product->price }}, size: "M", quantity: 1, image: @json($firstImage ? "storage/" . $firstImage->image_path : ""), code: @json($product->product_code), slug: @json($product->slug) }); setTimeout(() => added = false, 1500)'
                                class="mt-2.5 w-full py-2 rounded-xl text-xs font-semibold transition-all duration-300"
                                :class="added ? 'bg-green-500 text-white' : 'bg-gray-900 text-white hover:bg-gray-800'">
                                <span x-show="!added">Add to Cart</span>
                                <span x-show="added" x-cloak>✓ Added</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C]">
                    View All New Arrivals
                    <i class="fas fa-chevron-right w-4 h-4"></i>
                </a>
            </div>
        </div>
    </section>

    {{-- Banner Divider --}}
    <section class="bg-gradient-to-r from-[#E85D2C] to-[#F97316] py-14">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-bold text-white">Custom Jersey Design</h2>
            <p class="mt-2 text-orange-100 text-sm sm:text-base max-w-lg mx-auto">Design your team's unique look. Choose colors, patterns, and add your club name & number.</p>
            <a href="{{ route('shop.products.index') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-[#E85D2C] font-semibold text-sm hover:bg-orange-50 transition-colors">
                Start Designing
                <i class="fas fa-arrow-right w-4 h-4"></i>
            </a>
        </div>
    </section>

    {{-- Trending --}}
    <section class="py-14 lg:py-18 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">Popular</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">Top Selling</h2>
                </div>
                <a href="{{ route('shop.products.index') }}" class="hidden sm:inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C] hover:text-[#d14d1f] transition-colors">
                    View All
                    <i class="fas fa-chevron-right w-4 h-4"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ($trending as $product)
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
                            <div class="mt-1">
                                <span class="text-base font-bold text-[#E85D2C]">৳{{ number_format($product->price) }}</span>
                            </div>
                            <button @click='added = true; addToCart({ id: {{ $product->id }}, name: @json($product->product_name), price: {{ $product->price }}, size: "M", quantity: 1, image: @json($firstImage ? "storage/" . $firstImage->image_path : ""), code: @json($product->product_code), slug: @json($product->slug) }); setTimeout(() => added = false, 1500)'
                                class="mt-2.5 w-full py-2 rounded-xl text-xs font-semibold transition-all duration-300"
                                :class="added ? 'bg-green-500 text-white' : 'bg-gray-900 text-white hover:bg-gray-800'">
                                <span x-show="!added">Add to Cart</span>
                                <span x-show="added" x-cloak>✓ Added</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6 text-center sm:hidden">
                <a href="{{ route('shop.products.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-[#E85D2C]">
                    View All Top Selling
                    <i class="fas fa-chevron-right w-4 h-4"></i>
                </a>
            </div>
        </div>
    </section>

    @include('shop.components.features')
@endsection
