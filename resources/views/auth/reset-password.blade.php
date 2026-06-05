<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Reset Password — {{ config('app.name') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#0F1117] text-[#E6EDF3] antialiased">
    <div class="flex min-h-screen items-center justify-center px-4">
        <div class="w-full max-w-sm">
            <div class="mb-8 text-center">
                <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#3B82F6]">
                    <i class="fas fa-lock h-7 w-7 text-white"></i>
                </div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Set New Password</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Choose a strong password</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-6">
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="space-y-2">
                        <label for="email" class="text-sm font-medium text-[#E6EDF3]">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                            class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        @error('email')
                        <p class="text-sm text-[#EF4444]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4 space-y-2">
                        <label for="password" class="text-sm font-medium text-[#E6EDF3]">New Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        @error('password')
                        <p class="text-sm text-[#EF4444]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-4 space-y-2">
                        <label for="password_confirmation" class="text-sm font-medium text-[#E6EDF3]">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                            class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    </div>

                    <button type="submit"
                        class="mt-6 w-full rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-2 focus:ring-offset-[#0F1117]">
                        Reset Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>