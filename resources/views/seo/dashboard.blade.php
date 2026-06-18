<x-layouts.app title="SEO Dashboard">
    <div class="space-y-6">
        <h1 class="text-2xl font-bold text-[#E6EDF3]">SEO Dashboard</h1>

        {{-- Health Score --}}
        <x-card class="p-6">
            <div class="flex items-center gap-6">
                <div class="relative">
                    <svg class="w-24 h-24 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="#232A36" stroke-width="3"></circle>
                        <circle cx="18" cy="18" r="15.5" fill="none" stroke="{{ $healthScore >= 80 ? '#22C55E' : ($healthScore >= 50 ? '#F59E0B' : '#EF4444') }}" stroke-width="3" stroke-dasharray="{{ $healthScore * 0.865 }}, 100" stroke-linecap="round"></circle>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-2xl font-bold text-[#E6EDF3]">{{ $healthScore }}</span>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#E6EDF3]">SEO Health Score</h3>
                    <p class="text-sm text-[#94A3B8]">{{ $healthScore >= 80 ? 'Good' : ($healthScore >= 50 ? 'Needs Improvement' : 'Critical') }}</p>
                </div>
            </div>
        </x-card>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#E6EDF3]">{{ $stats['total_seo_records'] }}</p>
                <p class="text-xs text-[#94A3B8]">Total SEO Records</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $stats['active'] }}</p>
                <p class="text-xs text-[#94A3B8]">Active</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#6B7280]">{{ $stats['inactive'] }}</p>
                <p class="text-xs text-[#94A3B8]">Inactive</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#EF4444]">{{ $stats['missing_description'] }}</p>
                <p class="text-xs text-[#94A3B8]">Missing Descriptions</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#EF4444]">{{ $stats['missing_title'] }}</p>
                <p class="text-xs text-[#94A3B8]">Missing Titles</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#F59E0B]">{{ $stats['missing_schema'] }}</p>
                <p class="text-xs text-[#94A3B8]">Missing Schema</p>
            </x-card>
        </div>

        {{-- Alerts --}}
        <x-card class="p-6">
            <h3 class="mb-4 text-lg font-semibold text-[#E6EDF3]">Alerts & Issues</h3>
            <div class="space-y-3">
                @forelse($alerts['duplicate_titles'] as $title)
                <div class="flex items-start gap-3 rounded-xl bg-[#F59E0B]/10 p-3">
                    <i class="fas fa-exclamation-triangle mt-0.5 text-[#F59E0B]"></i>
                    <div>
                        <p class="text-sm font-medium text-[#E6EDF3]">Duplicate Title</p>
                        <p class="text-xs text-[#94A3B8]">{{ $title }}</p>
                    </div>
                </div>
                @empty
                @if($alerts['duplicate_descriptions']->isNotEmpty())
                    @foreach($alerts['duplicate_descriptions'] as $description)
                    <div class="flex items-start gap-3 rounded-xl bg-[#F59E0B]/10 p-3">
                        <i class="fas fa-exclamation-triangle mt-0.5 text-[#F59E0B]"></i>
                        <div>
                            <p class="text-sm font-medium text-[#E6EDF3]">Duplicate Description</p>
                            <p class="text-xs text-[#94A3B8]">{{ Str::limit($description, 100) }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                <p class="text-sm text-[#94A3B8]">No issues detected.</p>
                @endif
                @endforelse
            </div>
        </x-card>

        {{-- Quick Actions --}}
        <div class="flex gap-3">
            <form method="POST" action="{{ admin_route('seo.auto-generate') }}" onsubmit="return confirm('Run auto SEO generation?')" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#22C55E] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#16A34A]">
                    <i class="fas fa-magic"></i> Auto-Generate Missing
                </button>
            </form>
            <form method="POST" action="{{ admin_route('seo.audit') }}" onsubmit="return confirm('Run full SEO audit?')" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                    <i class="fas fa-search"></i> Run Full Audit
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
