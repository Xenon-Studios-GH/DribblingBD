<x-layouts.app title="Website Products">
    <div class="space-y-6" x-data="websiteProjects()">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Products</h1>
        </div>

        @if (session('success'))
        <div class="rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/10 px-4 py-3 text-sm text-[#22C55E]">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/10 px-4 py-3 text-sm text-[#EF4444]">
            {{ session('error') }}
        </div>
        @endif

        <x-card>
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Search</label>
                    <input type="text" x-model="filters.search" @input.debounce.500ms="fetchProjects()" placeholder="Search by name or code..." autocomplete="off"
                        class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Category</label>
                    <select x-model="filters.category_id" @change="fetchProjects()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->parent ? $cat->parent->name . ' > ' . $cat->name : $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Status</label>
                    <select x-model="filters.status" @change="fetchProjects()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Status</option>
                        <option value="complete">Complete</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="resetFilters()" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>
                </div>
            </div>
        </x-card>

        <div id="projectsContainer" x-html="tableHtml" class="space-y-6">
            @include('website.projects._table')
        </div>
    </div>

    <script>
        function websiteProjects() {
            return {
                filters: {
                    search: '',
                    category_id: '',
                    status: '',
                },
                page: 1,
                tableHtml: '',
                loading: false,

                init() {
                    this.fetchProjects();
                    window.websiteProjectsGoToPage = (p) => { this.page = p; this.fetchProjects(); };
                },

                fetchProjects() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', this.page);

                    fetch('{{ admin_route('website.projects') }}?' + params.toString(), {
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
                    this.filters = { search: '', category_id: '', status: '' };
                    this.page = 1;
                    this.fetchProjects();
                },
            };
        }
    </script>
</x-layouts.app>
