@php
    $pageTitle = isset($category) ? ($category->parent ? $category->parent->name . ' - ' . $category->name : $category->name) : 'All Projects';
@endphp

@extends('shop.layouts.shop', ['title' => $pageTitle])

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <a href="/" class="hover:text-[#E85D2C]">Home</a>
            <span>/</span>
            @if(isset($category))
            <a href="{{ route('shop.projects') }}" class="hover:text-[#E85D2C]">Projects</a>
            <span>/</span>
            <span class="text-gray-900 truncate">{{ $category->parent ? $category->parent->name . ' > ' . $category->name : $category->name }}</span>
            @else
            <span class="text-gray-900">Projects</span>
            @endif
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Sidebar --}}
            <aside class="lg:w-64 shrink-0">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Categories</h2>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('shop.projects') }}" class="block text-sm {{ !isset($category) ? 'text-[#E85D2C] font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                            All Projects
                        </a>
                    </li>
                    @foreach($categories as $cat)
                    <li>
                        <a href="{{ route('shop.category', $cat->parent?->slug ?? $cat->slug) }}" class="block text-sm {{ isset($category) && $category->id === $cat->id ? 'text-[#E85D2C] font-medium' : 'text-gray-600 hover:text-gray-900' }}">
                            {{ $cat->parent ? $cat->parent->name . ' > ' . $cat->name : $cat->name }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </aside>

            {{-- Grid --}}
            <div class="flex-1">
                @if(isset($category))
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $category->parent ? $category->parent->name . ' - ' . $category->name : $category->name }}</h1>
                @if($category->description)
                <p class="text-gray-600 mb-6">{{ $category->description }}</p>
                @endif
                @endif

                @if($projects->count() === 0)
                <div class="text-center py-16">
                    <p class="text-gray-500">{{ $settings['ui_no_projects'] ?? 'No projects found in this category.' }}</p>
                </div>
                @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($projects as $project)
                    <div class="group bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        <a href="{{ shop_project_url($project) }}" class="block">
                            <div class="aspect-square bg-gray-100 overflow-hidden">
                                @php $firstImage = $project->images->first(); @endphp
                                @if($firstImage)
                                <img src="{{ asset('storage/' . $firstImage->image_path) }}" alt="{{ $project->product->product_name }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-[#E85D2C] transition-colors">{{ $project->product->product_name }}</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-sm text-gray-500">{{ $project->category?->parent?->name ?? $project->category?->name ?? 'Uncategorized' }}</span>
                                </div>
                                <div class="mt-2 flex items-center gap-2">
                                    <span class="text-lg font-bold text-gray-900">৳{{ number_format($project->regular_price, 2) }}</span>
                                    @if($project->offer_price)
                                    <span class="text-sm text-gray-400 line-through">৳{{ number_format($project->offer_price, 2) }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $projects->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection
