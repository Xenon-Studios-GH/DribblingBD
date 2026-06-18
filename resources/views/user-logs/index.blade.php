<x-layouts.app title="User Logs">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">User Logs</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">View system logs for different user types.</p>
        </div>

        <x-card>
            <form method="GET" action="{{ route('user-logs.index') }}" class="flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Type</label>
                    <select name="type" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="work" {{ request('type') == 'work' ? 'selected' : '' }}>Work</option>
                        <option value="admin" {{ request('type') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="clients" {{ request('type') == 'clients' ? 'selected' : '' }}>Clients</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Search</label>
                    <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-5 py-2 text-sm font-medium text-white hover:bg-[#2563EB]">Filter</button>
                    <a href="{{ route('user-logs.index') }}" class="rounded-xl border border-[#232A36] px-5 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</a>
                </div>
            </form>
        </x-card>

        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36]">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User/Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#232A36]">
                        @forelse ($logs as $log)
                        <tr class="transition-colors hover:bg-[#1C2333]">
                            <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $log->user_id ?? $log->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#3B82F6]/10 text-[#3B82F6]">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-[#94A3B8] max-w-[300px] truncate" title="Action: {{ $log->action }} | IP: {{ $log->ip_address ?? 'N/A' }}">
                                {{ $log->action }} action performed. IP: {{ $log->ip_address ?? 'Unknown' }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-right text-[#94A3B8]">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                                No logs found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-layouts.app>
