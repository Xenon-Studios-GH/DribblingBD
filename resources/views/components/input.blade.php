@props(['label' => null, 'name' => null, 'id' => null, 'type' => 'text', 'error' => null])

@php
$id = $id ?? $name;
@endphp

<div {{ $attributes->whereDoesntStartWith('wire:model') }}>
    @if ($label)
    <label for="{{ $id }}" class="mb-2 block text-sm font-medium text-[#E6EDF3]">{{ $label }}</label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $attributes->whereStartsWith('wire:model') }}
        @error($name) aria-invalid="true" @enderror
        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6] @error($name) border-[#EF4444] @enderror">

    @if ($error)
    <p class="mt-1 text-sm text-[#EF4444]">{{ $error }}</p>
    @endif
</div>