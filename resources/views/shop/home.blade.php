@extends('shop.layouts.shop', ['title' => 'Home'])

@section('content')
    {{-- Hero Section --}}
    <section class="relative min-h-screen bg-white flex items-center overflow-hidden">
        <div class="w-full max-w-[1440px] mx-auto">
            <div class="grid lg:grid-cols-[45%_55%] gap-8 lg:gap-16 items-center min-h-[calc(100vh-160px)]">
                {{-- Left Content --}}
                <div class="pb-16 lg:pb-0" x-data="heroAnimation()" x-init="init()">
                    {{-- Heading --}}
                    <h1 class="text-[42px] sm:text-[56px] lg:text-[72px] xl:text-[88px] font-black leading-[0.9] tracking-[-3px] text-black"
                        :class="loaded ? 'animate-fade-up' : 'opacity-0'">
                        <span class="block">Your Identity.</span>
                        <span class="block delay-200" :class="loaded ? 'animate-fade-up' : 'opacity-0'">
                            <span class="text-[#E85D2C]">Your Jersey.</span>
                            <span> Your Game.</span>
                        </span>
                    </h1>

                    {{-- Description --}}
                    <p class="mt-8 text-[18px] text-[#7A7A7A] leading-[1.8] max-w-[500px]"
                       :class="loaded ? 'animate-fade-up delay-500' : 'opacity-0'">
                        Premium custom jerseys for clubs, tournaments, and champions.
                        Design your look, own the pitch.
                    </p>

                    {{-- CTA --}}
                    <div class="mt-10" :class="loaded ? 'animate-scale-in delay-800' : 'opacity-0 scale-0'">
                        <a href="{{ route('shop.products.index') }}"
                           class="inline-flex items-center justify-center w-[180px] h-[58px] rounded-full bg-black text-white font-semibold text-sm hover:scale-105 transition-transform duration-300 shadow-[0_15px_40px_rgba(0,0,0,0.15)]">
                            Shop Now
                        </a>
                    </div>
                </div>

                {{-- Right Image --}}
                <div class="flex justify-center"
                     x-data="heroAnimation()" x-init="init()"
                     :class="loaded ? 'animate-slide-in-right' : 'opacity-0'">
                    <div class="max-w-[500px] rounded-3xl overflow-hidden bg-gray-100 shadow-lg">
                        @php $heroImage = $heroImages->first(); @endphp
                        @if ($heroImage)
                        <img src="{{ asset('storage/' . $heroImage) }}"
                             alt="Premium Jersey"
                             class="w-full h-full object-cover scale-110">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-box w-16 h-16 text-gray-300"></i>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

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
            <a href="https://wa.me/{{ config('shop.whatsapp_number') }}?text={{ urlencode('Hi, I want to design a custom jersey') }}" target="_blank" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-[#E85D2C] font-semibold text-sm hover:bg-orange-50 transition-colors">
                <i class="fab fa-whatsapp w-5 h-5"></i>
                Design on WhatsApp
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
