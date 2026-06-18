<x-layouts.app title="Redirect Manager">
    <div class="space-y-6" x-data="redirectManager()">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Redirect Manager</h1>
            <a href="{{ admin_route('seo.redirects.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                <i class="fas fa-plus"></i> Add Redirect
            </a>
        </div>

        <x-card>
            <div class="flex flex-wrap gap-3">
                <input type="text" x-model="filters.search" @input.debounce="fetchRedirects()" placeholder="Search URLs..." class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none w-64">
                <select x-model="filters.status" @change="fetchRedirects()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <select x-model="filters.match_type" @change="fetchRedirects()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    <option value="">All Types</option>
                    <option value="exact">Exact</option>
                    <option value="prefix">Prefix</option>
                    <option value="regex">Regex</option>
                </select>
                <button @click="resetFilters()" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>
            </div>
        </x-card>

        <div id="redirectsContainer" x-html="tableHtml" class="space-y-4">
            @include('seo.redirects._table')
        </div>
    </div>

    <script>
        function redirectManager() {
            return {
                filters: { search: '', status: '', match_type: '' },
                page: 1,
                tableHtml: '',
                loading: false,

                init() {
                    this.fetchRedirects();
                    window.redirectGoToPage = (p) => { this.page = p; this.fetchRedirects(); };
                },

                fetchRedirects() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', this.page);

                    fetch('{{ admin_route('seo.redirects.index') }}?' + params.toString(), {
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
                    this.filters = { search: '', status: '', match_type: '' };
                    this.page = 1;
                    this.fetchRedirects();
                },
            };
        }
    </script>
</x-layouts.app>
