<x-layouts.app title="{{ isset($pixel) ? 'Edit Pixel' : 'Add Pixel' }}">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('tracking.index', ['role' => request()->route('role')]) }}" class="text-[#94A3B8] hover:text-[#E6EDF3] transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">{{ isset($pixel) ? 'Edit Pixel' : 'Add Pixel' }}</h1>
        </div>

        <x-card class="p-6">
            <form method="POST" action="{{ isset($pixel) ? route('tracking.update', ['role' => request()->route('role'), 'trackingPixel' => $pixel->id]) : route('tracking.store', ['role' => request()->route('role')]) }}" class="space-y-4">
                @csrf @if(isset($pixel)) @method('PUT') @endif

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Platform</label>
                        <select name="platform" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none" required>
                            <option value="">Select platform...</option>
                            @foreach($platforms as $val => $label)
                                <option value="{{ $val }}" {{ (old('platform', $pixel->platform ?? '') === $val) ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('platform') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Name</label>
                        <input type="text" name="name" value="{{ old('name', $pixel->name ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none" required>
                        @error('name') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Pixel ID / Tag ID / Measurement ID</label>
                    <input type="text" name="pixel_id" value="{{ old('pixel_id', $pixel->pixel_id ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] font-mono focus:border-[#3B82F6] focus:outline-none" required>
                    @error('pixel_id') <p class="mt-1 text-xs text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Load Position</label>
                        <select name="load_position" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <option value="head" {{ (old('load_position', $pixel->load_position ?? 'head') === 'head') ? 'selected' : '' }}>Head</option>
                            <option value="body" {{ (old('load_position', $pixel->load_position ?? 'head') === 'body') ? 'selected' : '' }}>Body</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">Sort Order</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $pixel->sort_order ?? 0) }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none" min="0">
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $pixel->is_active ?? true) ? 'checked' : '' }} class="h-4 w-4 rounded border-[#232A36] bg-[#161B22] text-[#3B82F6] focus:ring-[#3B82F6]">
                        <span class="text-sm text-[#E6EDF3]">Active</span>
                    </label>
                </div>

                {{-- Meta-specific: CAPI Token --}}
                <div id="meta-options" class="hidden space-y-4 border-t border-[#232A36] pt-4">
                    <h4 class="text-sm font-medium text-[#E6EDF3]">Conversion API (CAPI) Settings</h4>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-[#94A3B8]">CAPI Access Token</label>
                        <input type="password" name="options[capi_token]" value="{{ old('options.capi_token', $pixel->options['capi_token'] ?? '') }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] font-mono focus:border-[#3B82F6] focus:outline-none">
                        <p class="mt-1 text-xs text-[#94A3B8]">From Meta Business Suite → Events Manager → Settings → Conversions API</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-6 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        {{ isset($pixel) ? 'Update Pixel' : 'Create Pixel' }}
                    </button>
                    <a href="{{ route('tracking.index', ['role' => request()->route('role')]) }}" class="text-sm text-[#94A3B8] hover:text-[#E6EDF3] transition-colors">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const platform = document.querySelector('[name="platform"]');
        const metaOptions = document.getElementById('meta-options');
        function toggleMetaOptions() {
            if (platform.value === 'meta') {
                metaOptions.classList.remove('hidden');
            } else {
                metaOptions.classList.add('hidden');
            }
        }
        platform.addEventListener('change', toggleMetaOptions);
        toggleMetaOptions();
    });
    </script>
</x-layouts.app>
