@props(['title' => '', 'description' => ''])

<div {{ $attributes->merge(['class' => 'mb-8']) }}>
    <h1 class="text-2xl font-bold text-[#E6EDF3]">{{ $title }}</h1>
    @if ($description)
    <p class="mt-1 text-sm text-[#94A3B8]">{{ $description }}</p>
    @endif
</div>