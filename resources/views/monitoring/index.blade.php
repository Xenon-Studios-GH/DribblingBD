<x-layouts.app title="Monitoring">
    <div class="space-y-6" x-data="monitoring()">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Monitoring</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">System activity and audit logs across all modules.</p>
        </div>

        <!-- Toggle Pills -->
        <div class="flex flex-wrap gap-2">
            <template x-for="pill in tabs" :key="pill.key">
                <button @click="switchTab(pill.key)"
                    class="rounded-full px-5 py-2 text-sm font-medium transition-colors"
                    :class="tab === pill.key
                        ? 'bg-[#2563EB] text-white'
                        : 'border border-[#232A36] text-[#94A3B8] hover:bg-[#1C2333] hover:text-white'"
                    x-text="pill.label">
                </button>
            </template>
        </div>

        <!-- Filters -->
        <x-card x-show="tab !== 'traps'">
            <div class="flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-full md:min-w-[220px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Search</label>
                    <input type="text" x-model="search" @input.debounce.300ms="fetch()"
                        placeholder="Search actions, descriptions, modules..."
                        autocomplete="off"
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">User</label>
                    <select x-model="userId" @change="fetch()"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div x-show="availableModules.length > 1">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Module</label>
                    <select x-model="module" @change="fetch()"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Modules</option>
                        <template x-for="m in availableModules" :key="m.value">
                            <option x-bind:value="m.value" x-text="m.label"></option>
                        </template>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">From</label>
                    <input type="date" x-model="dateFrom" @change="fetch()"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">To</label>
                    <input type="date" x-model="dateTo" @change="fetch()"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button @click="resetFilters()"
                        class="rounded-xl border border-[#232A36] px-5 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>

                </div>
            </div>
        </x-card>

        <!-- Loading indicator -->
        <div x-show="loading" class="text-center py-4">
            <i class="fas fa-spinner fa-spin text-[#3B82F6] text-xl"></i>
        </div>

        <!-- Table container -->
        <div id="monitoringTable" x-show="tab !== 'traps'" x-html="logsHtml" x-cloak>
            @include('monitoring._table')
        </div>

        <!-- Traps table container -->
        <div id="trapsTable" x-show="tab === 'traps'" x-html="trapsHtml" x-cloak>
            @if (isset($traps))
                @include('monitoring._traps_table')
            @endif
        </div>
    </div>

    <script>
        function monitoring() {
            return {
                tab: @js(request('tab', 'all')),
                search: @js(request('search', '')),
                userId: @js(request('user_id', '')),
                module: @js(request('module', '')),
                dateFrom: @js(request('date_from', '')),
                dateTo: @js(request('date_to', '')),
                page: 1,
                logsHtml: '',
                trapsHtml: '',
                loading: false,

                tabs: [
                    { key: 'all', label: 'All' },
                    { key: 'login', label: 'Login Logs' },
                    { key: 'orders', label: 'Orders Logs' },
                    { key: 'stock', label: 'Stock Logs' },
                    { key: 'finance', label: 'Finance Logs' },
                    { key: 'web', label: 'Web Logs' },
                    { key: 'automation', label: 'Automation' },
                    { key: 'traps', label: 'Traps' },
                ],

                moduleOptions: {
                    all:     [
                        { value: 'system', label: 'System' },
                        { value: 'user', label: 'User' },
                        { value: 'order', label: 'Order' },
                        { value: 'stock', label: 'Stock' },
                        { value: 'finance', label: 'Finance' },
                        { value: 'website', label: 'Website' },
                        { value: 'seo', label: 'SEO' },
                        { value: 'inquiry', label: 'Inquiry' },
                    ],
                    login:   [{ value: 'system', label: 'System' }],
                    orders:  [{ value: 'order', label: 'Order' }],
                    stock:   [{ value: 'stock', label: 'Stock' }],
                    finance: [{ value: 'finance', label: 'Finance' }],
                    web:     [
                        { value: 'website', label: 'Website' },
                        { value: 'seo', label: 'SEO' },
                    ],
                    automation: [{ value: 'Audit', label: 'Audit' }],
                },

                get availableModules() {
                    return this.moduleOptions[this.tab] || this.moduleOptions.all;
                },

                init() {
                    this.logsHtml = document.getElementById('monitoringTable').innerHTML;
                    this.trapsHtml = document.getElementById('trapsTable').innerHTML;
                    window.monitoringGoToPage = (p) => { this.page = p; this.fetch(); };
                },

                switchTab(newTab) {
                    this.tab = newTab;
                    this.module = '';
                    this.page = 1;
                    if (newTab === 'traps') {
                        this.search = '';
                        this.userId = '';
                        this.dateFrom = '';
                        this.dateTo = '';
                    }
                    this.fetch();
                },

                fetch() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    if (this.tab && this.tab !== 'all') params.append('tab', this.tab);

                    if (this.tab === 'traps') {
                        fetch('{{ admin_route('monitoring.index') }}?' + params.toString(), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(r => r.text())
                        .then(html => {
                            this.trapsHtml = html;
                            this.loading = false;
                            const url = new URL(window.location);
                            url.search = params.toString();
                            window.history.replaceState({}, '', url);
                        })
                        .catch(() => { this.loading = false; });
                        return;
                    }

                    if (this.search) params.append('search', this.search);
                    if (this.userId) params.append('user_id', this.userId);
                    if (this.module) params.append('module', this.module);
                    if (this.dateFrom) params.append('date_from', this.dateFrom);
                    if (this.dateTo) params.append('date_to', this.dateTo);
                    params.append('page', this.page);

                    fetch('{{ admin_route('monitoring.index') }}?' + params.toString(), {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(r => r.text())
                    .then(html => {
                        this.logsHtml = html;
                        this.loading = false;
                        const url = new URL(window.location);
                        url.search = params.toString();
                        window.history.replaceState({}, '', url);
                    })
                    .catch(() => { this.loading = false; });
                },

                resetFilters() {
                    this.search = '';
                    this.userId = '';
                    this.module = '';
                    this.dateFrom = '';
                    this.dateTo = '';
                    this.tab = 'all';
                    this.page = 1;
                    this.fetch();
                },


            }
        }
    </script>
</x-layouts.app>
