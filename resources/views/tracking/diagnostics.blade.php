<x-layouts.app title="Tracking Diagnostics">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">Tracking Diagnostics</h1>

        {{-- Summary --}}
        <div class="grid grid-cols-3 gap-4">
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#E6EDF3]">{{ $health['total_pixels'] }}</p>
                <p class="text-xs text-[#94A3B8]">Total Pixels</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#22C55E]">{{ $health['active_pixels'] }}</p>
                <p class="text-xs text-[#94A3B8]">Active</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $health['capi_enabled'] ? 'ON' : 'OFF' }}</p>
                <p class="text-xs text-[#94A3B8]">CAPI</p>
            </x-card>
        </div>

        {{-- Debug Mode Toggle --}}
        <x-card class="p-6">
            <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">Debug Mode</h3>
            <p class="mb-4 text-sm text-[#94A3B8]">When enabled, all tracking events are logged to the browser console.</p>
            <form method="POST" action="{{ route('tracking.diagnostics.toggle-debug', ['role' => request()->route('role')]) }}" class="flex items-center gap-4">
                @csrf
                <input type="hidden" name="enabled" value="0">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="enabled" value="1" {{ $health['debug_mode'] ? 'checked' : '' }} onchange="this.form.submit()" class="h-4 w-4 rounded border-[#232A36] bg-[#161B22] text-[#3B82F6] focus:ring-[#3B82F6]">
                    <span class="text-sm text-[#E6EDF3]">{{ $health['debug_mode'] ? 'Enabled' : 'Disabled' }}</span>
                </label>
            </form>
        </x-card>

        {{-- Pixel Health --}}
        <x-card class="p-6">
            <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">Pixel Health</h3>
            <div class="space-y-4">
                @foreach($health['pixels'] as $pixel)
                <div class="rounded-lg border border-[#232A36] bg-[#161B22] p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-2 w-2 rounded-full {{ $pixel['is_active'] ? 'bg-[#22C55E]' : 'bg-[#EF4444]' }}"></span>
                            <span class="font-medium text-[#E6EDF3]">{{ $pixel['name'] }}</span>
                            <span class="text-xs text-[#94A3B8]">({{ $pixel['platform_label'] }})</span>
                        </div>
                        @if($pixel['has_capi'])
                            <span class="text-xs text-[#22C55E]">CAPI ready</span>
                        @endif
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-xs text-[#94A3B8]">
                        <div>
                            <span class="text-[#64748B]">Last event:</span>
                            <span class="ml-1 text-[#E6EDF3]">{{ $pixel['last_event_sent'] }}</span>
                        </div>
                        <div>
                            <span class="text-[#64748B]">Total events:</span>
                            <span class="ml-1 text-[#E6EDF3]">{{ $pixel['total_events'] }}</span>
                        </div>
                        <div>
                            <span class="text-[#64748B]">Failed:</span>
                            <span class="ml-1 {{ $pixel['failed_events'] > 0 ? 'text-[#EF4444]' : 'text-[#22C55E]' }}">{{ $pixel['failed_events'] }}</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <form action="{{ route('tracking.diagnostics.test', ['role' => request()->route('role'), 'trackingPixel' => $pixel['id']]) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg bg-[#1C2333] px-3 py-1.5 text-xs text-[#3B82F6] hover:bg-[#232A36] transition-colors">Fire Test Event</button>
                        </form>
                    </div>
                </div>
                @endforeach
                @if(empty($health['pixels']))
                    <p class="text-center text-sm text-[#94A3B8] py-4">No active pixels configured</p>
                @endif
            </div>
        </x-card>
    </div>
</x-layouts.app>
