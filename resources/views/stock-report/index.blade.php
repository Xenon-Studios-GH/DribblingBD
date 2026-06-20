<x-layouts.app title="Stock Report">
    <div x-data="stockReport()">
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-[#E6EDF3]">Stock Report</h1>
                    <p class="mt-1 text-sm text-[#94A3B8]">View and export stock movement history.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a :href="pdfUrl()"
                       class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] shadow-lg shadow-[#3B82F6]/20 transition-colors">
                        <i class="fas fa-file-pdf"></i> Generate PDF
                    </a>
                </div>
            </div>

            <x-card>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                        @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year', 'custom' => 'Custom'] as $key => $label)
                        <a href="#"
                           @click.prevent="setPeriod('{{ $key }}')"
                           :class="{
                               'bg-[#3B82F6] text-white': period === '{{ $key }}',
                               'text-[#94A3B8] hover:text-[#E6EDF3]': period !== '{{ $key }}',
                           }"
                           class="px-4 py-2 text-xs font-medium transition-colors {{ $period === $key ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]' }} {{ !$loop->first ? 'border-l border-[#232A36]' : '' }}">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                    <template x-if="period === 'day'">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar text-[#6B7280] text-sm"></i>
                            <input type="date" x-model="date" @change="fetchReport()"
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
                            <button @click="fetchReport()"
                                class="rounded-xl bg-[#3B82F6] px-4 py-2 text-xs font-semibold text-white hover:bg-[#2563EB] transition-colors">
                                Go
                            </button>
                        </div>
                    </template>
                </div>
            </x-card>

            <div class="relative">
                <div x-show="loading"
                     class="absolute inset-0 z-10 flex items-center justify-center rounded-xl bg-[#0F1117]/60">
                    <div class="flex items-center gap-3 rounded-xl bg-[#161B22] px-6 py-4 shadow-xl border border-[#232A36]">
                        <div class="animate-spin h-5 w-5 border-2 border-[#3B82F6] border-t-transparent rounded-full"></div>
                        <span class="text-sm text-[#94A3B8]">Loading...</span>
                    </div>
                </div>
                <div x-show="!loading" id="reportContent">
                    @include('stock-report._content')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function stockReport() {
            return {
                period: '{{ $period }}',
                date: '{{ $date }}',
                dateFrom: '{{ request('date_from', '') }}',
                dateTo: '{{ request('date_to', '') }}',
                loading: false,
                pdfUrl() {
                    const params = new URLSearchParams();
                    params.set('period', this.period);
                    if (this.period === 'custom') {
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                    } else {
                        params.set('date', this.date);
                    }
                    return '{{ route('stock.report.pdf') }}?' + params.toString();
                },
                fetchReport() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    params.set('period', this.period);
                    if (this.period === 'day') {
                        params.set('date', this.date);
                    } else if (this.period === 'custom') {
                        if (this.dateFrom) params.set('date_from', this.dateFrom);
                        if (this.dateTo) params.set('date_to', this.dateTo);
                    }
                    const url = '{{ route('stock.report') }}?' + params.toString();
                    fetch(url, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(data => {
                        document.getElementById('reportContent').innerHTML = data.html;
                        this.loading = false;
                        history.pushState(null, '', url);
                    })
                    .catch(() => { this.loading = false; });
                },
                setPeriod(newPeriod) {
                    this.period = newPeriod;
                    currentPeriod = newPeriod;
                    if (newPeriod === 'day') {
                        this.date = '{{ now()->toDateString() }}';
                    }
                    this.fetchReport();
                }
            };
        }

        var currentPeriod = '{{ $period }}';

        document.addEventListener('click', function(e) {
            var target = e.target;
            var content = document.getElementById('reportContent');
            if (!content || !content.contains(target)) return;

            var link = target.closest('a[href*="page="]');
            if (link) {
                e.preventDefault();
                fetch(link.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    document.getElementById('reportContent').innerHTML = data.html;
                    window.scrollTo({ top: content.offsetTop - 80, behavior: 'smooth' });
                });
                return;
            }

            var link = target.closest('a');
            if (link) return;
        });
    </script>
    @endpush
</x-layouts.app>
