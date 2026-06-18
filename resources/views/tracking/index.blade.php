<x-layouts.app title="Pixel Manager">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Pixel Manager</h1>
            <a href="{{ route('tracking.create', ['role' => request()->route('role')]) }}" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                + Add Pixel
            </a>
        </div>

        <div class="overflow-hidden rounded-xl border border-[#232A36]">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[#232A36] bg-[#161B22] text-left text-xs font-medium uppercase text-[#94A3B8]">
                        <th class="px-4 py-3">Platform</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Pixel ID</th>
                        <th class="px-4 py-3">Position</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#232A36]">
                    @forelse($pixels as $pixel)
                    <tr class="bg-[#0F1117] hover:bg-[#161B22] transition-colors">
                        <td class="px-4 py-3 text-sm text-[#E6EDF3]">
                            <span class="inline-flex items-center gap-1.5">
                                @switch($pixel->platform)
                                    @case('meta') <i class="fab fa-facebook text-[#1877F2]"></i> @break
                                    @case('ga4') <i class="fab fa-google text-[#4285F4]"></i> @break
                                    @case('gtm') <i class="fas fa-tag text-[#F9AB00]"></i> @break
                                    @case('google_ads') <i class="fas fa-ad text-[#4285F4]"></i> @break
                                    @case('clarity') <i class="fas fa-chart-line text-[#6C5CE7]"></i> @break
                                @endswitch
                                {{ $pixel->platform_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-[#E6EDF3] font-medium">{{ $pixel->name }}</td>
                        <td class="px-4 py-3 text-sm text-[#94A3B8] font-mono">{{ $pixel->pixel_id_masked }}</td>
                        <td class="px-4 py-3 text-sm text-[#94A3B8]">
                            <span class="rounded-md bg-[#1C2333] px-2 py-0.5 text-xs">{{ $pixel->load_position }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex h-5 w-10 items-center rounded-full p-0.5 transition-colors {{ $pixel->is_active ? 'bg-[#22C55E]' : 'bg-[#232A36]' }}">
                                <span class="inline-block h-4 w-4 rounded-full bg-white transition-transform {{ $pixel->is_active ? 'translate-x-5' : 'translate-x-0.5' }}"></span>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tracking.edit', ['role' => request()->route('role'), 'trackingPixel' => $pixel->id]) }}" class="rounded-lg px-2 py-1 text-xs text-[#3B82F6] hover:bg-[#1C2333] transition-colors">Edit</a>
                                <form action="{{ route('tracking.toggle', ['role' => request()->route('role'), 'trackingPixel' => $pixel->id]) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="rounded-lg px-2 py-1 text-xs {{ $pixel->is_active ? 'text-[#F59E0B]' : 'text-[#22C55E]' }} hover:bg-[#1C2333] transition-colors">
                                        {{ $pixel->is_active ? 'Disable' : 'Enable' }}
                                    </button>
                                </form>
                                <form action="{{ route('tracking.destroy', ['role' => request()->route('role'), 'trackingPixel' => $pixel->id]) }}" method="POST" class="inline" onsubmit="return confirm('Delete this pixel?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="rounded-lg px-2 py-1 text-xs text-[#EF4444] hover:bg-[#1C2333] transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-sm text-[#94A3B8]">
                            <p class="mb-2">No tracking pixels configured</p>
                            <a href="{{ route('tracking.create', ['role' => request()->route('role')]) }}" class="text-[#3B82F6] hover:underline">Add your first pixel</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
