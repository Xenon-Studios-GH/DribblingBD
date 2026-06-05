<x-layouts.app title="Login Logs">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Login Logs</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Track all login activity.</p>
        </div>

        <x-card>
            <form method="GET" class="flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-full md:min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">User</label>
                    <select name="user_id" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id')==$user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Status</label>
                    <select name="status" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Status</option>
                        <option value="success" @selected(request('status')==='success' )>Success</option>
                        <option value="failed" @selected(request('status')==='failed' )>Failed</option>
                        <option value="logout" @selected(request('status')==='logout' )>Logout</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB]">Filter</button>
                <a href="{{ route('login-logs.index') }}" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</a>
            </form>
        </x-card>

        <x-card padding="p-0" class="hidden lg:block">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36]">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Login Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Logout Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#232A36]">
                        @forelse ($logs as $log)
                        <tr class="transition-colors hover:bg-[#1C2333]">
                            <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->email }}</td>
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-[#94A3B8]">{{ $log->ip_address ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->login_at ? $log->login_at->format('M d, Y H:i:s') : '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->logout_at ? $log->logout_at->format('M d, Y H:i:s') : '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $log->status === 'success' ? 'bg-[#22C55E]/10 text-[#22C55E]' : ($log->status === 'failed' ? 'bg-[#EF4444]/10 text-[#EF4444]' : 'bg-[#F59E0B]/10 text-[#F59E0B]') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                                No login logs found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($logs->hasPages())
            <div class="border-t border-[#232A36] px-6 py-3">
                {{ $logs->links() }}
            </div>
            @endif
        </x-card>

        <div class="block lg:hidden space-y-3">
            @forelse ($logs as $log)
            <x-card class="space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium 
                            {{ $log->status === 'success' ? 'bg-[#22C55E]/10 text-[#22C55E]' : 
                               ($log->status === 'failed' ? 'bg-[#EF4444]/10 text-[#EF4444]' : 
                                'bg-[#F59E0B]/10 text-[#F59E0B]') }}">
                        {{ ucfirst($log->status) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#94A3B8]">Email</span>
                    <span class="text-[#94A3B8]">{{ $log->email }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#94A3B8]">IP</span>
                    <span class="font-mono text-xs text-[#94A3B8]">{{ $log->ip_address ?? '—' }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#94A3B8]">Login</span>
                    <span class="text-[#94A3B8]">{{ $log->login_at ? $log->login_at->format('M d, Y H:i:s') : '—' }}</span>
                </div>
                @if ($log->logout_at)
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#94A3B8]">Logout</span>
                    <span class="text-[#94A3B8]">{{ $log->logout_at->format('M d, Y H:i:s') }}</span>
                </div>
                @endif
            </x-card>
            @empty
            <x-card class="py-12 text-center">
                <p class="text-sm text-[#94A3B8]">No login logs found.</p>
            </x-card>
            @endforelse
            @if ($logs->hasPages())
            <div class="pt-3">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>