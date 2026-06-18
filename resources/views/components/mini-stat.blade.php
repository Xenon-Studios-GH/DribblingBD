@props(['icon', 'color', 'label', 'value', 'sub' => null, 'href' => null])

@if ($href)
<a href="{{ $href }}" class="flex items-center gap-2.5 px-2 py-1.5 rounded-lg hover:bg-[#1C2333] transition-colors">
@else
<div class="flex items-center gap-2.5 px-2 py-1.5">
@endif
    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg" style="background-color: {{ $color }}10;">
        <i class="fas fa-{{ $icon }}" style="color: {{ $color }}; font-size: 11px;"></i>
    </div>
    <div class="min-w-0 flex-1">
        <p class="text-xs text-[#94A3B8] truncate">{{ $label }}</p>
        <p class="text-sm font-bold text-[#E6EDF3]">{{ $value }}@if($sub) <span class="text-[10px] font-normal text-[#6B7280]">· {{ $sub }}</span>@endif</p>
    </div>
@if ($href)
</a>
@else
</div>
@endif
