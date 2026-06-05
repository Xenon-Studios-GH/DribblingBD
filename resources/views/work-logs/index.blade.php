<x-layouts.app title="Work Logs">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Work Logs</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Audit trail of all system actions.</p>
        </div>

        <x-card>
            <form method="GET" class="flex flex-col md:flex-row md:flex-wrap items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-full md:min-w-[200px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">User</label>
                    <select name="user_id" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Users</option>
                        @foreach ($users as $user)
                        <option value="{{ $user->id }}" @selected(request('user_id')==$user->id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Module</label>
                    <select name="module" class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All Modules</option>
                        <option value="system" @selected(request('module')==='system' )>System</option>
                        <option value="user" @selected(request('module')==='user' )>User</option>
                        <option value="stock" @selected(request('module')==='stock' )>Stock</option>
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
                <a href="{{ route('work-logs.index') }}" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Reset</a>
            </form>
        </x-card>

        <x-card padding="p-0" class="hidden lg:block">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36]">
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#232A36]">
                        @forelse ($logs as $log)
                        <tr class="transition-colors hover:bg-[#1C2333]">
                            <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#3B82F6]/10 text-[#3B82F6]">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ ucfirst($log->module) }}</td>
                            <td class="px-6 py-4 text-[#94A3B8]">{{ $log->description ?? '—' }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                                No work logs found.
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
            <x-card class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-[#E6EDF3]">{{ $log->user?->name ?? '—' }}</span>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-[#3B82F6]/10 text-[#3B82F6]">
                        {{ $log->action }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[#94A3B8]">Module</span>
                    <span class="text-[#94A3B8]">{{ ucfirst($log->module) }}</span>
                </div>
                <div class="text-sm text-[#94A3B8]">{{ $log->description ?? '—' }}</div>
                <div class="text-xs text-[#94A3B8]">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
            </x-card>
            @empty
            <x-card class="py-12 text-center">
                <p class="text-sm text-[#94A3B8]">No work logs found.</p>
            </x-card>
            @endforelse
            @if ($logs->hasPages())
            <div class="pt-3">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>
</x-layouts.app>