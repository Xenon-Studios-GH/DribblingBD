@props(['active' => false, 'href' => '#', 'icon' => 'link', 'badge' => false])

@php
$classes = $active
? 'flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm font-medium text-[#E6EDF3] bg-[#1C2333] transition-colors'
: 'flex items-center gap-2.5 rounded-lg px-2.5 py-2 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} title="{{ trim($slot) }}">
    @if ($icon === 'dashboard')
    <i class="fas fa-home w-4 h-4"></i>
    @elseif ($icon === 'stock')
    <i class="fas fa-cubes w-4 h-4"></i>
    @elseif ($icon === 'stockin')
    <i class="fas fa-plus-circle w-4 h-4"></i>
    @elseif ($icon === 'stockout')
    <i class="fas fa-minus-circle w-4 h-4"></i>
    @elseif ($icon === 'users')
    <i class="fas fa-users w-4 h-4"></i>
    @elseif ($icon === 'login')
    <i class="fas fa-sign-in-alt w-4 h-4"></i>
    @elseif ($icon === 'activity')
    <i class="fas fa-chart-bar w-4 h-4"></i>
    @elseif ($icon === 'plus')
    <i class="fas fa-plus w-4 h-4"></i>
    @elseif ($icon === 'chart')
    <i class="fas fa-chart-pie w-4 h-4"></i>
    @elseif ($icon === 'transaction')
    <i class="fas fa-exchange-alt w-4 h-4"></i>
    @elseif ($icon === 'category')
    <i class="fas fa-tags w-4 h-4"></i>
    @elseif ($icon === 'project')
    <span class="relative inline-flex">
        <i class="fas fa-folder w-4 h-4"></i>
        @if ($badge)
        <span class="absolute -top-1.5 -right-1.5 flex h-2 w-2">
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EF4444] opacity-75"></span>
            <span class="relative inline-flex rounded-full h-2 w-2 bg-[#EF4444]"></span>
        </span>
        @endif
    </span>
    @elseif ($icon === 'report')
    <i class="fas fa-file-alt w-4 h-4"></i>
    @elseif ($icon === 'bell')
    <i class="fas fa-bell w-4 h-4"></i>
    @elseif ($icon === 'order')
    <i class="fas fa-receipt w-4 h-4"></i>
    @elseif ($icon === 'cog')
    <i class="fas fa-cog w-4 h-4"></i>
    @endif
    <span>{{ $slot }}</span>
</a>