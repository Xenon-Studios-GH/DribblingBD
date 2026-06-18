@props(['label', 'count', 'color', 'href' => null])

@if ($href)
<a href="{{ $href }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-[#1C2333] transition-colors">
@else
<div class="flex items-center justify-between px-2 py-1.5">
@endif
    <div class="flex items-center gap-2">
        <span class="h-2 w-2 rounded-full" style="background-color: {{ $color }}"></span>
        <span class="text-xs text-[#94A3B8]">{{ $label }}</span>
    </div>
    <span class="text-sm font-bold" style="color: {{ $color }}">{{ $count }}</span>
@if ($href)
</a>
@else
</div>
@endif
