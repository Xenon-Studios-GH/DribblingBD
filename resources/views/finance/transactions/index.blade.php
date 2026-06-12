<x-layouts.app title="Transactions">
    <div class="space-y-6" x-data="financeTransactions()">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Transactions</h1>
            <a href="{{ admin_route('finance.transactions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                <i class="fas fa-plus"></i> Add Transaction
            </a>
        </div>

        <x-card>
            <div class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
                <select x-model="filters.type" @change="fetchTransactions()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3]">
                    <option value="">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
                <select x-model="filters.category_id" @change="fetchTransactions()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3]">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <input type="date" x-model="filters.date_from" @change="fetchTransactions()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3]">
                <input type="date" x-model="filters.date_to" @change="fetchTransactions()" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2.5 text-sm text-[#E6EDF3]">
                <button @click="resetFilters()" class="rounded-xl border border-[#232A36] px-4 py-2.5 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>
            </div>
        </x-card>

        <div x-html="tableHtml" class="space-y-6">
            @include('finance.transactions._table')
        </div>
    </div>

    @push('scripts')
    <script>
        function financeTransactions() {
            return {
                filters: {
                    type: '',
                    category_id: '',
                    date_from: '',
                    date_to: '',
                },
                page: 1,
                tableHtml: '',
                loading: false,

                init() {
                    this.fetchTransactions();
                    window.financeGoToPage = (p) => { this.page = p; this.fetchTransactions(); };
                },

                fetchTransactions() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', this.page);

                    fetch('{{ admin_route('finance.transactions') }}?' + params.toString(), {
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
                    this.filters = { type: '', category_id: '', date_from: '', date_to: '' };
                    this.page = 1;
                    this.fetchTransactions();
                },
            };
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Transaction?',
                text: 'This will soft-delete the transaction and notify all admins.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete',
                background: '#161B22',
                color: '#E6EDF3',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>
