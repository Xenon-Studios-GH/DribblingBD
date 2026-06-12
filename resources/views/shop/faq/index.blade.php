@extends('shop.layouts.shop', ['title' => 'FAQs'])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
    <div class="text-center mb-12">
        <h1 class="text-3xl lg:text-4xl font-bold text-gray-900">Frequently Asked Questions</h1>
        <p class="text-sm text-gray-500 mt-2">Everything you need to know about DribblingBD</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-10">
        {{-- About Product --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                    <i class="fas fa-tshirt text-[#E85D2C]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">About Product</h2>
            </div>
            <div class="space-y-3" x-data="{ open: null }">
                @forelse ($productFaqs as $i => $faq)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-200"
                     :class="open === {{ $i }} ? 'shadow-md' : 'hover:shadow-sm'">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-gray-900 pr-4">{{ $faq->question }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 flex-shrink-0 text-xs"
                           :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse>
                        <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No product FAQs available yet.</p>
                @endforelse
            </div>
        </div>

        {{-- About Us & Orders --}}
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-blue-500"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">About Us & Orders</h2>
            </div>
            <div class="space-y-3" x-data="{ open: null }">
                @forelse ($orderFaqs as $i => $faq)
                <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-200"
                     :class="open === {{ $i }} ? 'shadow-md' : 'hover:shadow-sm'">
                    <button @click="open = open === {{ $i }} ? null : {{ $i }}"
                            class="w-full flex items-center justify-between px-5 py-4 text-left">
                        <span class="text-sm font-semibold text-gray-900 pr-4">{{ $faq->question }}</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform duration-200 flex-shrink-0 text-xs"
                           :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="open === {{ $i }}" x-collapse>
                        <div class="px-5 pb-5 text-sm text-gray-600 leading-relaxed">
                            {{ $faq->answer }}
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500">No order FAQs available yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
