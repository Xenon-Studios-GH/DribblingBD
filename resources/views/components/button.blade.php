@props(['variant' => 'primary', 'type' => 'button'])

@php
$classes = match ($variant) {
'primary' => 'bg-[#3B82F6] text-white hover:bg-[#2563EB] focus:ring-[#3B82F6]',
'success' => 'bg-[#22C55E] text-white hover:bg-[#16A34A] focus:ring-[#22C55E]',
'danger' => 'bg-[#EF4444] text-white hover:bg-[#DC2626] focus:ring-[#EF4444]',
'ghost' => 'bg-transparent text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3] focus:ring-[#232A36]',
default => 'bg-[#3B82F6] text-white hover:bg-[#2563EB] focus:ring-[#3B82F6]',
};
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-medium transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0F1117] ' . $classes]) }}>
    {{ $slot }}
</button>