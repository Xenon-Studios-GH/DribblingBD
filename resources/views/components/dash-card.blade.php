@props(['href' => null, 'icon', 'color', 'label', 'value'])

@if ($href)
<a href="{{ $href }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-3 transition-all duration-200 hover:bg-[#1C2333]" style="--card-color: {{ $color }};">
@else
<div class="rounded-xl border border-[#232A36] bg-[#161B22] p-3" style="--card-color: {{ $color }};">
@endif
    <div class="flex items-center justify-between">
        <div class="min-w-0">
            <p class="text-[10px] text-[#94A3B8] truncate">{{ $label }}</p>
            <p class="mt-0.5 text-base font-bold text-[#E6EDF3] truncate" style="color: var(--card-color)">{{ $value }}</p>
        </div>
        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg" style="background-color: {{ $color }}15;">
            <i class="fas fa-{{ $icon }}" style="color: {{ $color }}; font-size: 13px;"></i>
        </div>
    </div>
@if ($href)
</a>
@else
</div>
@endif
