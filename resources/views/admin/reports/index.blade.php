<x-layouts.app title="Reports & Analytics">
    <div x-data="reportsAnalytics()">
        <div class="space-y-6">
            {{-- Header --}}
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#E6EDF3]">Reports & Analytics</h1>
                    <p class="mt-1 text-sm text-[#94A3B8]">View and manage your business reports.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button @click="generatePdf()"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] shadow-lg shadow-[#3B82F6]/20 transition-colors">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </button>
                </div>
            </div>

            {{-- Period Toggle --}}
            <x-card>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                        @foreach (['all' => 'All', 'day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year', 'custom' => 'Custom'] as $key => $label)
                        <a href="#"
                           @click.prevent="setPeriod('{{ $key }}')"
                           :class="{
                               'bg-[#3B82F6] text-white': period === '{{ $key }}',
                               'text-[#94A3B8] hover:text-[#E6EDF3]': period !== '{{ $key }}',
                           }"
                           class="px-4 py-2 text-xs font-medium transition-colors {{ !$loop->first ? 'border-l border-[#232A36]' : '' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                    <template x-if="period === 'day'">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-[#6B7280] text-sm"></i>
                            <input type="date" x-model="date" @change="fetchData()"
                                class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        </div>
                    </template>
                    <template x-if="period === 'custom'">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-[#6B7280] text-sm"></i>
                            <input type="date" x-model="dateFrom"
                                class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                            <span class="text-[#6B7280] text-xs">to</span>
                            <input type="date" x-model="dateTo"
                                class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                            <button @click="fetchData()"
                                class="rounded-xl bg-[#3B82F6] px-4 py-2 text-xs font-semibold text-white hover:bg-[#2563EB] transition-colors">
                                Go
                            </button>
                        </div>
                    </template>
                </div>
            </x-card>

            {{-- Tabs --}}
            <div class="flex gap-1 border-b border-[#232A36]">
                @foreach (['all' => 'All', 'orders' => 'Orders Report', 'stock' => 'Stock Report', 'finance' => 'Finance'] as $key => $label)
                <button @click="setTab('{{ $key }}')"
                    :class="{
                        'border-b-2 border-[#3B82F6] text-[#E6EDF3]': activeTab === '{{ $key }}',
                        'text-[#94A3B8] hover:text-[#E6EDF3]': activeTab !== '{{ $key }}',
                    }"
                    class="px-4 py-3 text-sm font-medium transition-colors -mb-px">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- Search & Filters --}}
            <x-card>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="relative flex-1 min-w-[200px]">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-[#6B7280] text-sm"></i>
                        <input type="text" x-model="search" placeholder="Search..."
                            @keydown.enter="fetchData()"
                            class="w-full rounded-xl border border-[#232A36] bg-[#161B22] pl-9 pr-3 py-2 text-sm text-[#E6EDF3] placeholder:text-[#6B7280] focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    </div>
                    {{-- Stock type filter --}}
                    <template x-if="activeTab === 'stock'">
                        <select x-model="filters.type" @change="fetchData()"
                            class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">All Types</option>
                            <option value="in">Stock In</option>
                            <option value="out">Stock Out</option>
                        </select>
                    </template>
                    {{-- Orders status filter --}}
                    <template x-if="activeTab === 'orders'">
                        <select x-model="filters.status" @change="fetchData()"
                            class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="packed">Packed</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </template>
                    {{-- Finance chart type filter --}}
                    <template x-if="activeTab === 'finance'">
                        <select x-model="filters.chart" @change="fetchData()"
                            class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="pnl">P&L Trend</option>
                            <option value="monthly">Monthly Comparison</option>
                            <option value="category">Category Breakdown</option>
                            <option value="cashflow">Cash Flow</option>
                        </select>
                    </template>
                    <template x-if="activeTab === 'finance' && filters.chart === 'category'">
                        <select x-model="filters.type" @change="fetchData()"
                            class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                        </select>
                    </template>
                    <button @click="resetFilters()"
                        class="rounded-xl border border-[#232A36] px-4 py-2 text-sm font-medium text-[#94A3B8] hover:text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
            </x-card>

            {{-- Content Area with Loading --}}
            <div class="relative">
                <div x-show="loading"
                     class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-[#0F1117]/60">
                    <div class="flex items-center gap-3 rounded-xl bg-[#161B22] px-6 py-4 shadow-xl border border-[#232A36]">
                        <div class="animate-spin h-5 w-5 border-2 border-[#3B82F6] border-t-transparent rounded-full"></div>
                        <span class="text-sm text-[#94A3B8]">Loading...</span>
                    </div>
                </div>
                <div x-show="!loading" x-html="content" id="reportContent"></div>
            </div>
        </div>

        {{-- Slide-out Panel --}}
        <template x-teleport="body">
            <div x-show="slideOutOpen" x-cloak
                 x-transition:enter="transition-transform duration-300"
                 x-transition:enter-start="translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition-transform duration-200"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="translate-x-full"
                 class="fixed top-0 right-0 z-50 h-full w-full max-w-2xl border-l border-[#232A36] bg-[#161B22] shadow-2xl overflow-y-auto">
                <div class="sticky top-0 z-10 flex items-center justify-between border-b border-[#232A36] bg-[#161B22] px-6 py-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Details</h2>
                    <button @click="closeSlideOut()"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6" x-html="slideOutHtml"></div>
            </div>
        </template>

        {{-- Backdrop for slide-out --}}
        <div x-show="slideOutOpen" x-cloak
             x-transition:enter="transition-opacity duration-300"
             x-transition:leave="transition-opacity duration-200"
             class="fixed inset-0 z-40 bg-black/50"
             @click="closeSlideOut()"></div>
    </div>

    @push('scripts')
    <script>
        function reportsAnalytics() {
            return {
                activeTab: 'all',
                period: 'all',
                date: '{{ now()->toDateString() }}',
                dateFrom: '',
                dateTo: '',
                search: '',
                filters: {},
                loading: false,
                content: '',
                slideOutOpen: false,
                slideOutHtml: '',

                init() {
                    this.fetchData();
                    this.setupEventDelegation();
                },

                setupEventDelegation() {
                    document.addEventListener('click', (e) => {
                        const content = document.getElementById('reportContent');
                        if (!content || !content.contains(e.target)) return;

                        const viewBtn = e.target.closest('[data-view-url]');
                        if (viewBtn) {
                            e.preventDefault();
                            this.openSlideOut(viewBtn.dataset.viewUrl);
                            return;
                        }

                        const pageLink = e.target.closest('a[href*="page="]');
                        if (pageLink) {
                            e.preventDefault();
                            this.loading = true;
                            fetch(pageLink.href, {
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                            })
                            .then(r => r.json())
                            .then(data => {
                                this.content = data.html;
                                this.loading = false;
                                this.$nextTick(() => this.initCharts());
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            })
                            .catch(() => { this.loading = false; });
                            return;
                        }

                        const anyLink = e.target.closest('a');
                        if (anyLink && content.contains(anyLink) && anyLink.href) {
                            const isInternal = anyLink.href.startsWith(window.location.origin);
                            if (!isInternal) return;
                        }
                    });
                },

                fetchData() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    params.set('tab', this.activeTab);
                    params.set('period', this.period);
                    if (this.period === 'day') {
                        params.set('date', this.date);
                    } else if (this.period === 'custom') {
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                    }
                    if (this.search) params.set('search', this.search);
                    Object.keys(this.filters).forEach(k => {
                        if (this.filters[k]) params.set('filters[' + k + ']', this.filters[k]);
                    });

                    const url = '{{ route('admin.reports.data') }}?' + params.toString();
                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.content = data.html;
                        this.loading = false;
                        this.$nextTick(() => this.initCharts());
                    })
                    .catch(() => { this.loading = false; });
                },

                initCharts() {
                    document.querySelectorAll('[data-chart]').forEach(canvas => {
                        try {
                            const config = JSON.parse(canvas.dataset.chart);
                            if (window.Chart) {
                                new Chart(canvas, config);
                            }
                        } catch(e) {}
                    });
                },

                setTab(tab) {
                    this.activeTab = tab;
                    this.filters = {};
                    this.fetchData();
                },

                setPeriod(period) {
                    this.period = period;
                    if (period === 'day') {
                        this.date = '{{ now()->toDateString() }}';
                    }
                    this.fetchData();
                },

                openSlideOut(url) {
                    this.loading = true;
                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.slideOutHtml = data.html;
                        this.loading = false;
                        this.slideOutOpen = true;
                    })
                    .catch(() => { this.loading = false; });
                },

                closeSlideOut() {
                    this.slideOutOpen = false;
                    this.slideOutHtml = '';
                },

                resetFilters() {
                    this.search = '';
                    this.filters = {};
                    this.period = 'all';
                    this.fetchData();
                },

                generatePdf() {
                    const params = new URLSearchParams();
                    params.set('tab', this.activeTab);
                    params.set('period', this.period);
                    if (this.period === 'day') {
                        params.set('date', this.date);
                    } else if (this.period === 'custom') {
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                    }
                    window.open('{{ route('admin.reports.pdf') }}?' + params.toString(), '_blank');
                }
            };
        }
    </script>
    @endpush
</x-layouts.app>
