@props(['active' => false, 'href' => '#', 'icon' => 'link'])

@php
$classes = $active
? 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[#E6EDF3] bg-[#1C2333] transition-colors'
: 'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} title="{{ trim($slot) }}">
    @if ($icon === 'dashboard')
    <i class="fas fa-home w-5 h-5"></i>
    @elseif ($icon === 'stock')
    <i class="fas fa-cubes w-5 h-5"></i>
    @elseif ($icon === 'stockin')
    <i class="fas fa-plus-circle w-5 h-5"></i>
    @elseif ($icon === 'stockout')
    <i class="fas fa-minus-circle w-5 h-5"></i>
    @elseif ($icon === 'users')
    <i class="fas fa-users w-5 h-5"></i>
    @elseif ($icon === 'login')
    <i class="fas fa-sign-in-alt w-5 h-5"></i>
    @elseif ($icon === 'activity')
    <i class="fas fa-chart-bar w-5 h-5"></i>
    @elseif ($icon === 'plus')
    <i class="fas fa-plus w-5 h-5"></i>
    @endif
    <span>{{ $slot }}</span>
</a>