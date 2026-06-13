@extends('shop.layouts.shop', ['title' => $project->product->product_name])

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-[#E85D2C]">Home</a>
            <span>/</span>
            <a href="{{ route('shop.projects') }}" class="hover:text-[#E85D2C]">Projects</a>
            <span>/</span>
            @if($project->category?->parent)
            <a href="{{ route('shop.category', $project->category->parent->slug) }}" class="hover:text-[#E85D2C]">{{ $project->category->parent->name }}</a>
            <span>/</span>
            <a href="{{ route('shop.category', $project->category->slug) }}" class="hover:text-[#E85D2C]">{{ $project->category->name }}</a>
            <span>/</span>
            @elseif($project->category)
            <a href="{{ route('shop.category', $project->category->slug) }}" class="hover:text-[#E85D2C]">{{ $project->category->name }}</a>
            <span>/</span>
            @endif
            <span class="text-gray-900 truncate">{{ $project->product->product_name }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Images --}}
            <div>
                @php
                $images = $project->images;
                $mainImage = $images->first();
                @endphp
                @if($mainImage)
                <div class="aspect-square bg-gray-100 rounded-xl overflow-hidden mb-3">
                    <img src="{{ storage_url($mainImage->image_path) }}" alt="{{ $project->product->product_name }}" class="w-full h-full object-cover">
                </div>
                @if($images->count() > 1)
                <div class="grid grid-cols-4 gap-2" style="overflow-x: auto; -webkit-overflow-scrolling: touch; scrollbar-width: thin;">
                    @foreach($images as $img)
                    <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden cursor-pointer border-2 border-transparent hover:border-[#E85D2C] transition-colors flex-shrink-0">
                        <img src="{{ storage_url($img->image_path) }}" alt="" loading="lazy" class="w-full h-full object-cover">
                    </div>
                    @endforeach
                </div>
                @endif
                @else
                <div class="aspect-square bg-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                @endif
            </div>

            {{-- Details --}}
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $project->product->product_name }}</h1>
                <p class="text-sm text-gray-500 mb-4">Code: {{ $project->product->product_code }}</p>

                @if($project->category)
                <div class="flex items-center gap-2 mb-4">
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-800">
                        {{ $project->category->parent ? $project->category->parent->name . ' > ' . $project->category->name : $project->category->name }}
                    </span>
                </div>
                @endif

                <div class="flex items-baseline gap-3 mb-6">
                    <span class="text-3xl font-bold text-gray-900">৳{{ number_format($project->regular_price, 2) }}</span>
                    @if($project->offer_price)
                    <span class="text-lg text-gray-400 line-through">৳{{ number_format($project->offer_price, 2) }}</span>
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Offer</span>
                    @endif
                </div>

                @if($project->details)
                <div class="prose prose-sm max-w-none text-gray-600 mb-6">
                    {!! nl2br(e($project->details)) !!}
                </div>
                @endif
            </div>
        </div>

        {{-- Related Projects --}}
        @if($relatedProjects->count() > 0)
        <div class="mt-16">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Related Projects</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProjects as $related)
                <div class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                    <a href="{{ shop_project_url($related) }}" class="block">
                        <div class="aspect-square bg-gray-100 overflow-hidden">
                            @php $firstImage = $related->images->first(); @endphp
                            @if($firstImage)
                            <img src="{{ storage_url($firstImage->image_path) }}" alt="{{ $related->product->product_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-semibold text-gray-900 group-hover:text-[#E85D2C] transition-colors text-sm">{{ $related->product->product_name }}</h3>
                            <span class="text-sm font-bold text-gray-900">৳{{ number_format($related->regular_price, 2) }}</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
@endsection
