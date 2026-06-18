<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="flex min-h-screen items-center justify-center bg-[#0F1117] text-[#E6EDF3] antialiased">
    <div class="text-center px-4">
        <h1 class="text-5xl md:text-6xl font-bold text-[#EF4444]">500</h1>
        <p class="mt-4 text-lg text-[#94A3B8]">Server Error.</p>
        <p class="mt-2 text-sm text-[#94A3B8]">Something went wrong. Please try again later.</p>
        @php
            $user = auth()->user();
            $dashUrl = $user ? route('dashboard') : url('/');
        @endphp
        <a href="{{ $user && $user->role !== 'customer' ? $dashUrl : url('/') }}" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-[#2563EB]">
            {{ $user && $user->role !== 'customer' ? 'Back to Dashboard' : 'Back to Home' }}
        </a>
    </div>
</body>

</html>
