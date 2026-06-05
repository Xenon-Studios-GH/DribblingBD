@props(['variant' => 'default'])

@php
$classes = match ($variant) {
'success' => 'bg-[#22C55E]/10 text-[#22C55E]',
'danger' => 'bg-[#EF4444]/10 text-[#EF4444]',
'warning' => 'bg-[#F59E0B]/10 text-[#F59E0B]',
'info' => 'bg-[#3B82F6]/10 text-[#3B82F6]',
default => 'bg-[#94A3B8]/10 text-[#94A3B8]',
};
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . $classes]) }}>
    {{ $slot }}
</span>