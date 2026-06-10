<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen items-center justify-center bg-[#0F1117] text-[#E6EDF3] antialiased">
    <div class="text-center px-4" x-data="{ countdown: 3 }" x-init="let t = setInterval(() => { countdown--; if (countdown <= 0) { clearInterval(t); window.location.href = '/'; } }, 1000)">
        <h1 class="text-5xl md:text-6xl font-bold text-[#EF4444]">404</h1>
        <p class="mt-4 text-lg text-[#E6EDF3]">Invalid URL</p>
        <p class="mt-2 text-sm text-[#94A3B8]">The page you requested is not registered in our system.</p>
        <p class="mt-4 text-sm text-[#6B7280]">
            Redirecting to homepage in <span class="text-[#3B82F6] font-medium" x-text="countdown"></span> seconds...
        </p>
        <a href="/" class="mt-6 inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-5 py-3 text-sm font-medium text-white transition-colors hover:bg-[#2563EB]">
            Go to Homepage
        </a>
    </div>
</body>

</html>