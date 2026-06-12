@extends('shop.layouts.shop', ['title' => 'Home'])

@push('styles')
<style>
    .cta {
        border: none;
        background: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0;
    }
    .cta span {
        padding-bottom: 7px;
        letter-spacing: 4px;
        font-size: 14px;
        padding-right: 15px;
        text-transform: uppercase;
    }
    .cta svg {
        transform: translateX(-8px);
        transition: all 0.3s ease;
    }
    .cta:hover svg {
        transform: translateX(0);
    }
    .cta:active svg {
        transform: scale(0.9);
    }
    .hover-underline-animation {
        position: relative;
        color: black;
        padding-bottom: 20px;
    }
    .hover-underline-animation:after {
        content: "";
        position: absolute;
        width: 100%;
        transform: scaleX(0);
        height: 2px;
        bottom: 0;
        left: 0;
        background-color: #000000;
        transform-origin: bottom right;
        transition: transform 0.25s ease-out;
    }
    .cta:hover .hover-underline-animation:after {
        transform: scaleX(1);
        transform-origin: bottom left;
    }
</style>
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="relative bg-white overflow-hidden">
        <div class="w-full max-w-[1440px] mx-auto px-4 sm:px-8 lg:px-[80px] py-10 lg:py-14">
            <div class="grid lg:grid-cols-[45%_55%] gap-8 lg:gap-16 items-center">
                {{-- Left Content --}}
                <div class="pb-16 lg:pb-0" x-data="heroAnimation()" x-init="init()">
                    {{-- Heading --}}
                    <h1 class="text-[42px] sm:text-[48px] md:text-[56px] lg:text-[64px] xl:text-[76px] font-black leading-[0.9] tracking-[-2px] sm:tracking-[-3px] text-black"
                        :class="loaded ? 'animate-fade-up' : 'opacity-0'">
                        <span class="block">{{ $settings['hero_heading_top'] ?? 'Your Identity.' }}</span>
                        <span class="block delay-200" :class="loaded ? 'animate-fade-up' : 'opacity-0'">
                            <span class="text-[#E85D2C]">{{ $settings['hero_heading_middle'] ?? 'Your Jersey.' }}</span>
                            <span> {{ $settings['hero_heading_bottom'] ?? 'Your Game.' }}</span>
                        </span>
                    </h1>

                    {{-- Description --}}
                    <p class="mt-8 text-[18px] text-[#7A7A7A] leading-[1.8] max-w-[500px]"
                       :class="loaded ? 'animate-fade-up delay-500' : 'opacity-0'">
                        {{ $settings['hero_subtitle'] ?? 'Premium custom jerseys for clubs, tournaments, and champions. Design your look, own the pitch.' }}
                    </p>

                    {{-- CTA --}}
                    <div class="mt-10" :class="loaded ? 'animate-scale-in delay-800' : 'opacity-0 scale-0'">
                        <a href="{{ $settings['hero_cta_link'] ?? route('shop.products.index') }}" class="cta">
                            <span class="hover-underline-animation">{{ $settings['hero_cta_text'] ?? 'Shop Now' }}</span>
                            <svg id="arrow-horizontal" xmlns="http://www.w3.org/2000/svg" width="30" height="10" viewBox="0 0 46 16">
                                <path id="Path_10" data-name="Path 10" d="M8,0,6.545,1.455l5.506,5.506H-30V9.039H12.052L6.545,14.545,8,16l8-8Z" transform="translate(30)"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Right Image --}}
                <div class="hidden sm:flex justify-center"
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

    {{-- Stats Section --}}
    <section class="bg-gray-50 border-y border-gray-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-[80px] py-14 lg:py-16">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-0">
                <div class="text-center lg:border-r border-gray-200/60 last:border-r-0 px-6"
                     x-data="counter($el, {{ $settings['stats_1_value'] ?? 100 }}, '{{ $settings['stats_1_suffix'] ?? '+' }}')" x-init="init()">
                    <p class="text-3xl lg:text-4xl font-bold text-[#E85D2C]" x-text="count + suffix">0</p>
                    <p class="text-sm text-[#7A7A7A] mt-1.5">{{ $settings['stats_1_label'] ?? 'Premium Products' }}</p>
                </div>
                <div class="text-center lg:border-r border-gray-200/60 last:border-r-0 px-6"
                     x-data="counter($el, {{ $settings['stats_2_value'] ?? 2000 }}, '{{ $settings['stats_2_suffix'] ?? '+' }}')" x-init="init()">
                    <p class="text-3xl lg:text-4xl font-bold text-[#E85D2C]" x-text="count + suffix">0</p>
                    <p class="text-sm text-[#7A7A7A] mt-1.5">{{ $settings['stats_2_label'] ?? 'Happy Customers' }}</p>
                </div>
                <div class="text-center lg:border-r border-gray-200/60 last:border-r-0 px-6"
                     x-data="counter($el, {{ $settings['stats_3_value'] ?? 7 }}, '{{ $settings['stats_3_suffix'] ?? ' mins' }}')" x-init="init()">
                    <p class="text-3xl lg:text-4xl font-bold text-[#E85D2C]">&lt;<span x-text="count">0</span>{{ $settings['stats_3_suffix'] ?? ' mins' }}</p>
                    <p class="text-sm text-[#7A7A7A] mt-1.5">{{ $settings['stats_3_label'] ?? 'Avg Reply Time' }}</p>
                </div>
                <div class="text-center px-6"
                     x-data="counter($el, {{ $settings['stats_4_value'] ?? 96 }}, '{{ $settings['stats_4_suffix'] ?? ' hours' }}')" x-init="init()">
                    <p class="text-3xl lg:text-4xl font-bold text-[#E85D2C]"><span x-text="count">0</span>{{ $settings['stats_4_suffix'] ?? ' hours' }}</p>
                    <p class="text-sm text-[#7A7A7A] mt-1.5">{{ $settings['stats_4_label'] ?? 'Avg Delivery Time' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-14 lg:py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">{{ $settings['new_arrivals_eyebrow'] ?? 'Latest' }}</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $settings['new_arrivals_heading'] ?? 'New Arrivals' }}</h2>
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
                            <img src="{{ asset('storage/' . $firstImage->image_path) }}" alt="{{ $product->product_name }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
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
                            <button @click='added = true; addToCart({ id: {{ $product->id }}, name: @json($product->product_name, JSON_HEX_TAG), price: {{ $product->price }}, size: "M", quantity: 1, image: @json($firstImage ? "storage/" . $firstImage->image_path : "", JSON_HEX_TAG), code: @json($product->product_code, JSON_HEX_TAG), slug: @json($product->slug, JSON_HEX_TAG) }); setTimeout(() => added = false, 1500)'
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
            <h2 class="text-2xl sm:text-3xl font-bold text-white">{{ $settings['banner_heading'] ?? 'Custom Jersey Design' }}</h2>
            <p class="mt-2 text-orange-100 text-sm sm:text-base max-w-lg mx-auto">{{ $settings['banner_subtext'] ?? "Design your team's unique look. Choose colors, patterns, and add your club name & number." }}</p>
            <a href="{{ $settings['banner_cta_link'] ?? 'https://wa.me/'.config('shop.whatsapp_number').'?text='.urlencode('Hi, I want to design a custom jersey') }}" target="_blank" class="mt-6 inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-[#E85D2C] font-semibold text-sm hover:bg-orange-50 transition-colors">
                <i class="fab fa-whatsapp w-5 h-5"></i>
                {{ $settings['banner_cta'] ?? 'Design on WhatsApp' }}
            </a>
        </div>
    </section>

    {{-- Trending --}}
    <section class="py-14 lg:py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">{{ $settings['top_selling_eyebrow'] ?? 'Popular' }}</span>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $settings['top_selling_heading'] ?? 'Top Selling' }}</h2>
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
                            <img src="{{ asset('storage/' . $firstImage->image_path) }}" alt="{{ $product->product_name }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                            <div class="absolute inset-0 flex items-center justify-center">
                                <i class="fas fa-box w-16 h-16 text-gray-300 group-hover:text-gray-400 transition-colors"></i>
                            </div>
                            @endif
                            <div class="absolute top-3 right-3 z-10">
                                <button @click.stop='toggleWishlist({ id: {{ $product->id }}, code: @json($product->product_code, JSON_HEX_TAG), name: @json($product->product_name, JSON_HEX_TAG) })' class="p-1.5 rounded-lg bg-white/80 hover:bg-white shadow-sm" aria-label="Add to wishlist">
                                    <i class="fas fa-heart w-4 h-4" :class="isInWishlist({{ $product->id }}) ? 'text-red-500' : 'text-gray-400'"></i>
                                </button>
                            </div>
                        </a>
                        <div class="p-3 sm:p-4">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $product->product_name }}</h3>
                            <div class="mt-1">
                                <span class="text-base font-bold text-[#E85D2C]">৳{{ number_format($product->price) }}</span>
                            </div>
                            <button @click='added = true; addToCart({ id: {{ $product->id }}, name: @json($product->product_name, JSON_HEX_TAG), price: {{ $product->price }}, size: "M", quantity: 1, image: @json($firstImage ? "storage/" . $firstImage->image_path : "", JSON_HEX_TAG), code: @json($product->product_code, JSON_HEX_TAG), slug: @json($product->slug, JSON_HEX_TAG) }); setTimeout(() => added = false, 1500)'
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

    {{-- Reviews Carousel --}}
    <section class="py-14 lg:py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
            <div class="text-center">
                <span class="text-xs font-semibold text-[#E85D2C] uppercase tracking-widest">{{ $settings['testimonials_eyebrow'] ?? 'Testimonials' }}</span>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">{{ $settings['testimonials_heading'] ?? 'What Our Customers Say' }}</h2>
            </div>
        </div>
        @if ($testimonials->isNotEmpty())
        <div class="flex animate-marquee whitespace-nowrap gap-6" style="animation-duration: 40s;">
            <div class="flex gap-6">
                @foreach ($testimonials as $t)
                <div class="w-[320px] inline-flex flex-col shrink-0 bg-gray-50 rounded-2xl p-6 text-left">
                    <div class="flex items-center gap-1 text-[#F59E0B] text-sm mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $t->rating ? '' : ' text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-normal">"{{ $t->content }}"</p>
                    <p class="mt-3 text-sm font-semibold text-gray-900">— {{ $t->name }}{{ $t->designation ? ' ('.$t->designation.')' : '' }}</p>
                </div>
                @endforeach
            </div>
            {{-- Duplicate for seamless loop --}}
            <div class="flex gap-6">
                @foreach ($testimonials as $t)
                <div class="w-[320px] inline-flex flex-col shrink-0 bg-gray-50 rounded-2xl p-6 text-left">
                    <div class="flex items-center gap-1 text-[#F59E0B] text-sm mb-3">
                        @for ($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $t->rating ? '' : ' text-gray-300' }}"></i>
                        @endfor
                    </div>
                    <p class="text-sm text-gray-600 leading-relaxed whitespace-normal">"{{ $t->content }}"</p>
                    <p class="mt-3 text-sm font-semibold text-gray-900">— {{ $t->name }}{{ $t->designation ? ' ('.$t->designation.')' : '' }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </section>

    @include('shop.components.features')
@endsection
