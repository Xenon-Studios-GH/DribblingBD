<x-layouts.app title="Login Logs">
    <div class="space-y-6" x-data="loginLogs()">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Login Logs</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Track all login activity.</p>
        </div>

        <x-card>
            <div class="flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-full md:min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">User</label>
                    <select x-model="filters.user_id" @change="fetchLogs()" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Status</label>
                    <select x-model="filters.status" @change="fetchLogs()" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Status</option>
                        <option value="success">Success</option>
                        <option value="failed">Failed</option>
                        <option value="logout">Logout</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">From</label>
                    <input type="date" x-model="filters.date_from" @change="fetchLogs()" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">To</label>
                    <input type="date" x-model="filters.date_to" @change="fetchLogs()" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button @click="resetFilters()" class="rounded-xl border border-[#232A36] px-5 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</button>
                </div>
            </div>
        </x-card>

        <div id="logsContainer" x-html="tableHtml" class="space-y-6">
            @include('login-logs._table')
        </div>
    </div>

    <script>
        function loginLogs() {
            return {
                filters: {
                    user_id: '',
                    status: '',
                    date_from: '',
                    date_to: '',
                },
                page: 1,
                tableHtml: '',
                loading: false,

                init() {
                    this.fetchLogs();
                    window.loginLogsGoToPage = (p) => { this.page = p; this.fetchLogs(); };
                },

                fetchLogs() {
                    if (this.loading) return;
                    this.loading = true;

                    const params = new URLSearchParams();
                    Object.entries(this.filters).forEach(([k, v]) => { if (v) params.append(k, v); });
                    params.append('page', this.page);

                    fetch('{{ admin_route('login-logs.index') }}?' + params.toString(), {
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
                    this.filters = { user_id: '', status: '', date_from: '', date_to: '' };
                    this.page = 1;
                    this.fetchLogs();
                },
            };
        }
    </script>
</x-layouts.app>
