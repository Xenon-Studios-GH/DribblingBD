@props(['padding' => 'p-6'])

<div {{ $attributes->merge(['class' => 'rounded-xl border border-[#232A36] bg-[#161B22] ' . $padding]) }}>
    {{ $slot }}
</div>