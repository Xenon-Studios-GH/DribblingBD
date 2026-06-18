<x-layouts.app title="Customize Charts">
    <div x-data="chartCustomizer()" x-init="init()">
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-[#E6EDF3]">Customize Charts</h1>
                    <p class="mt-1 text-sm text-[#94A3B8]">Choose which categories appear in your pie charts.</p>
                </div>
                <a href="{{ admin_route('finance.dashboard') }}" class="text-sm text-[#3B82F6] hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
            </div>

            {{-- Type Switcher + Period Switcher --}}
            <div class="flex flex-wrap items-center gap-4">
                <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                    <button @click="switchType('income')"
                        class="px-5 py-2 text-sm font-medium transition-colors"
                        :class="type === 'income' ? 'bg-[#22C55E] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]'">
                        <i class="fas fa-arrow-up mr-1"></i> Income
                    </button>
                    <button @click="switchType('expense')"
                        class="px-5 py-2 text-sm font-medium transition-colors border-l border-[#232A36]"
                        :class="type === 'expense' ? 'bg-[#EF4444] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]'">
                        <i class="fas fa-arrow-down mr-1"></i> Expense
                    </button>
                </div>

                <div class="flex rounded-lg overflow-hidden border border-[#232A36]">
                    @foreach (['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $label)
                    <button @click="switchPeriod('{{ $key }}')"
                        class="px-4 py-2 text-xs font-medium transition-colors {{ !$loop->first ? 'border-l border-[#232A36]' : '' }}"
                        :class="period === '{{ $key }}' ? 'bg-[#3B82F6] text-white' : 'text-[#94A3B8] hover:text-[#E6EDF3]'">
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left: Categories List --}}
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-[#E6EDF3]">
                            <span x-text="type === 'income' ? 'Income' : 'Expense'"></span> Categories
                        </h2>
                        <button @click="resetPreferences()" class="text-xs text-[#EF4444] hover:underline">
                            <i class="fas fa-undo mr-1"></i> Reset
                        </button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(cat, idx) in categories" :key="cat.id">
                            <label class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-[#1C2333] transition-colors cursor-pointer">
                                <input type="checkbox" :checked="cat.selected" @change="toggleCategory(cat.id)"
                                    class="h-4 w-4 rounded border-[#232A36] bg-[#0F1117] text-[#3B82F6] focus:ring-[#3B82F6]">
                                <div class="flex-1 flex items-center justify-between">
                                    <span class="text-sm text-[#E6EDF3]" x-text="cat.name"></span>
                                    <div class="flex items-center gap-3 text-xs">
                                        <span class="text-[#6B7280] w-12 text-right" x-text="cat.pct + '%'"></span>
                                        <span class="text-[#E6EDF3] font-medium w-28 text-right" x-text="'৳' + Number(cat.total).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                                    </div>
                                </div>
                            </label>
                        </template>
                    </div>
                    <div class="mt-4 pt-3 border-t border-[#232A36] flex items-center justify-between text-sm">
                        <span class="text-[#94A3B8]">Selected Total</span>
                        <span class="font-bold text-[#E6EDF3]" x-text="'৳' + Number(selectedTotal).toLocaleString(undefined, {minimumFractionDigits: 2})"></span>
                    </div>
                </x-card>

                {{-- Right: Pie Chart --}}
                <x-card>
                    <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Preview</h2>
                    <div class="flex flex-col items-center">
                        <div class="w-64 h-64">
                            <canvas id="customizePieChart"></canvas>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-[#94A3B8]">Total for selected categories</p>
                            <p class="text-2xl font-bold text-[#E6EDF3]" x-text="'৳' + Number(selectedTotal).toLocaleString(undefined, {minimumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </x-card>
            </div>

            <div class="flex justify-end">
                <button @click="savePreferences()" class="rounded-xl bg-[#3B82F6] px-6 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB] shadow-lg shadow-[#3B82F6]/20">
                    <i class="fas fa-save mr-2"></i> Save Preferences
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function chartCustomizer() {
            return {
                type: '{{ $type }}',
                period: '{{ $period }}',
                categories: {!! json_encode($categoryData) !!},
                chartData: {!! json_encode($chartData) !!},
                selectedTotal: {{ $selectedTotal }},
                grandTotal: {{ $grandTotal }},
                chart: null,
                chartColors: ['#22C55E', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#14B8A6', '#F97316', '#6366F1', '#06B6D4'],

                init() {
                    this.calcPercentages();
                    this.renderChart();
                },

                calcPercentages() {
                    const selected = this.categories.filter(c => c.selected);
                    const total = selected.reduce((sum, c) => sum + c.total, 0);
                    this.categories.forEach(c => {
                        c.pct = total > 0 && c.selected ? ((c.total / total) * 100).toFixed(1) : '0.0';
                    });
                    this.selectedTotal = total;
                    this.chartData = selected.filter(c => c.total > 0);
                    this.renderChart();
                },

                toggleCategory(id) {
                    const cat = this.categories.find(c => c.id === id);
                    if (cat) {
                        cat.selected = !cat.selected;
                        this.calcPercentages();
                    }
                },

                switchType(type) {
                    this.type = type;
                    this.fetchData();
                },

                switchPeriod(period) {
                    this.period = period;
                    this.fetchData();
                },

                fetchData() {
                    fetch(`{{ route('finance.customize-charts') }}?type=${this.type}&period=${this.period}`, {
                        headers: { 
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        this.categories = data.categories;
                        // chartData will be updated in calcPercentages
                        this.selectedTotal = data.selectedTotal;
                        this.grandTotal = data.grandTotal;
                        this.calcPercentages();
                    });
                },

                renderChart() {
                    if (this.chart) this.chart.destroy();
                    const ctx = document.getElementById('customizePieChart');
                    if (!ctx) return;
                    const labels = this.chartData.map(c => c.name);
                    const data = this.chartData.map(c => c.total);
                    const colors = this.chartData.map((_, i) => this.chartColors[i % this.chartColors.length]);
                    this.chart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels,
                            datasets: [{
                                data,
                                backgroundColor: colors,
                                borderColor: '#0F1117',
                                borderWidth: 2,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: true,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    callbacks: {
                                        label: (ctx) => ctx.label + ': ৳' + Number(ctx.raw).toLocaleString(undefined, {minimumFractionDigits: 2}),
                                    },
                                },
                            },
                        },
                    });
                },

                savePreferences() {
                    const selectedIds = this.categories.filter(c => c.selected).map(c => c.id);
                    fetch('{{ route('finance.customize-charts.update') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: this.type, selected_category_ids: selectedIds }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            alert('Preferences saved!');
                        }
                    });
                },

                resetPreferences() {
                    if (!confirm('Reset all category selections for this type?')) return;
                    fetch('{{ route('finance.customize-charts.reset') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ type: this.type }),
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) this.fetchData();
                    });
                },
            };
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-layouts.app>
