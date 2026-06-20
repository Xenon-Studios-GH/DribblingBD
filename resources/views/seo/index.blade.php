<x-layouts.app title="SEO Management">
    <div class="space-y-6" x-data="seoManagement()">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">SEO Management</h1>
            <div class="flex gap-2">
                <form method="POST" action="{{ admin_route('seo.auto-generate') }}" onsubmit="return confirm('Run auto SEO generation? This will overwrite template-based fields.')" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-[#22C55E] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#16A34A]">
                    <i class="fas fa-magic"></i> Auto-Generate
                </button></form>
            </div>
        </div>

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
                <p class="text-2xl font-bold text-[#3B82F6]">{{ $counts['products'] }}</p>
                <p class="text-xs text-[#94A3B8]">Products</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#8B5CF6]">{{ $counts['projects'] }}</p>
                <p class="text-xs text-[#94A3B8]">Projects</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#F59E0B]">{{ $counts['categories'] }}</p>
                <p class="text-xs text-[#94A3B8]">Categories</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#22C55E]">{{ $stats['active'] }}</p>
                <p class="text-xs text-[#94A3B8]">Active</p>
            </x-card>
            <x-card class="p-4 text-center">
                <p class="text-2xl font-bold text-[#EF4444]">{{ $stats['missing_title'] }}</p>
                <p class="text-xs text-[#94A3B8]">Missing Titles</p>
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

        </div>

        {{-- Filters --}}
        <x-card>
            <div class="flex flex-wrap gap-3">
                <select x-model="filters.type" @change="fetchSeo()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    <option value="">All Types</option>
                    <option value="App\Models\Product">Products</option>
                    <option value="App\Models\WebsiteProject">Projects</option>
                    <option value="App\Models\WebsiteCategory">Categories</option>
                </select>
                <select x-model="filters.autogenerated" @change="fetchSeo()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    <option value="">All Sources</option>
                    <option value="yes">Auto-Generated</option>
                    <option value="no">Manual</option>
                </select>
                <select x-model="filters.status" @change="fetchSeo()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button @click="resetFilters()" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>
            </div>
        </x-card>

        {{-- Table --}}
        <div id="seoContainer" x-html="tableHtml" class="space-y-4">
            @include('seo._table')
        </div>
    </div>

    <script>
        function seoManagement() {
            return {
                filters: {
                    type: '',
                    autogenerated: '',
                    status: '',
                },
                page: 1,
                tableHtml: '',
                loading: false,

                init() {
                    this.fetchSeo();
                    window.seoGoToPage = (p) => { this.page = p; this.fetchSeo(); };
                },

                fetchSeo() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', this.page);

                    fetch('{{ admin_route('seo.index') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.text())
                    .then(html => {
                        this.tableHtml = html;
                        this.loading = false;
                    })
                    .catch(() => { this.loading = false; });
                },

                resetFilters() {
                    this.filters = { type: '', autogenerated: '', status: '' };
                    this.page = 1;
                    this.fetchSeo();
                },
            };
        }
    </script>
</x-layouts.app>
