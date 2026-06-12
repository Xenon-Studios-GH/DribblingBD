<x-layouts.app title="Users">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Users</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Manage all user accounts.</p>
            </div>
            <a href="{{ admin_route('workers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#2563EB]">
                <i class="fas fa-plus h-4 w-4"></i>
                Add User
            </a>
        </div>

        @if (session('success'))
        <div class="rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/10 px-4 py-3 text-sm text-[#22C55E]">
            {{ session('success') }}
        </div>
        @endif

        @if (session('error'))
        <div class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/10 px-4 py-3 text-sm text-[#EF4444]">
            {{ session('error') }}
        </div>
        @endif

        @if ($admins->isNotEmpty())
        <div>
            <h2 class="mb-3 text-lg font-semibold text-[#E6EDF3]">Admins</h2>
            <x-card padding="p-0" class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36]">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Role</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @foreach ($admins as $admin)
                            <tr class="transition-colors hover:bg-[#1C2333]">
                                <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $admin->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $admin->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $admin->phone ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full {{ $admin->role === 'superadmin' ? 'bg-[#A855F7]/10 text-[#A855F7]' : 'bg-[#3B82F6]/10 text-[#3B82F6]' }} px-2.5 py-0.5 text-xs font-medium">
                                        {{ ucfirst($admin->role) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $admin->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                                        {{ $admin->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ admin_route('workers.edit', $admin) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/10">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ admin_route('workers.toggle-status', $admin) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#F59E0B] hover:bg-[#F59E0B]/10">
                                                {{ $admin->status ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <div class="block lg:hidden space-y-3">
                @foreach ($admins as $admin)
                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ $admin->name }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $admin->email }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $admin->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                            {{ $admin->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if ($admin->phone)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#94A3B8]">Phone</span>
                        <span class="text-[#94A3B8]">{{ $admin->phone }}</span>
                    </div>
                    @endif
                    <div class="flex items-center justify-between text-sm border-t border-[#232A36] pt-2">
                        <span class="text-[#94A3B8]">Role</span>
                        <span class="inline-flex items-center rounded-full {{ $admin->role === 'superadmin' ? 'bg-[#A855F7]/10 text-[#A855F7]' : 'bg-[#3B82F6]/10 text-[#3B82F6]' }} px-2.5 py-0.5 text-xs font-medium">{{ ucfirst($admin->role) }}</span>
                    </div>
                    <div class="flex gap-2 pt-2 border-t border-[#232A36]">
                        <a href="{{ admin_route('workers.edit', $admin) }}" class="flex-1 rounded-xl bg-[#3B82F6]/10 px-4 py-2.5 text-sm font-medium text-[#3B82F6] text-center hover:bg-[#3B82F6]/20">
                            Edit
                        </a>
                        <form method="POST" action="{{ admin_route('workers.toggle-status', $admin) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-[#F59E0B]/10 px-4 py-2.5 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20">
                                {{ $admin->status ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </x-card>
                @endforeach
            </div>
        </div>
        @endif

        <div>
            <h2 class="mb-3 text-lg font-semibold text-[#E6EDF3]">Staff</h2>
            <x-card padding="p-0" class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36]">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @forelse ($workers as $worker)
                            <tr class="transition-colors hover:bg-[#1C2333]">
                                <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $worker->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $worker->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $worker->phone ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $worker->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                                        {{ $worker->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ admin_route('workers.edit', $worker) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/10">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ admin_route('workers.toggle-status', $worker) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#F59E0B] hover:bg-[#F59E0B]/10">
                                                {{ $worker->status ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                                    No workers found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($workers->hasPages())
                <div class="border-t border-[#232A36] px-6 py-3">
                    {{ $workers->links() }}
                </div>
                @endif
            </x-card>

            <div class="block lg:hidden space-y-3">
                @forelse ($workers as $worker)
                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ $worker->name }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $worker->email }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $worker->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                            {{ $worker->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if ($worker->phone)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#94A3B8]">Phone</span>
                        <span class="text-[#94A3B8]">{{ $worker->phone }}</span>
                    </div>
                    @endif
                    <div class="flex gap-2 pt-2 border-t border-[#232A36]">
                        <a href="{{ admin_route('workers.edit', $worker) }}" class="flex-1 rounded-xl bg-[#3B82F6]/10 px-4 py-2.5 text-sm font-medium text-[#3B82F6] text-center hover:bg-[#3B82F6]/20">
                            Edit
                        </a>
                        <form method="POST" action="{{ admin_route('workers.toggle-status', $worker) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-[#F59E0B]/10 px-4 py-2.5 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20">
                                {{ $worker->status ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </x-card>
                @empty
                <x-card class="py-12 text-center">
                    <p class="text-sm text-[#94A3B8]">No workers found.</p>
                </x-card>
                @endforelse
                @if ($workers->hasPages())
                <div class="pt-3">{{ $workers->links() }}</div>
                @endif
            </div>
        </div>

        <div>
            <h2 class="mb-3 text-lg font-semibold text-[#E6EDF3]">Clients</h2>
            <x-card padding="p-0" class="hidden lg:block">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36]">
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#232A36]">
                            @forelse ($clients as $client)
                            <tr class="transition-colors hover:bg-[#1C2333]">
                                <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $client->name }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $client->email }}</td>
                                <td class="whitespace-nowrap px-6 py-4 text-[#94A3B8]">{{ $client->phone ?? '—' }}</td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $client->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                                        {{ $client->status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ admin_route('workers.edit', $client) }}" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/10">
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ admin_route('workers.toggle-status', $client) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="rounded-lg px-3 py-1.5 text-xs font-medium text-[#F59E0B] hover:bg-[#F59E0B]/10">
                                                {{ $client->status ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-sm text-[#94A3B8]">
                                    No clients found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($clients->hasPages())
                <div class="border-t border-[#232A36] px-6 py-3">
                    {{ $clients->links() }}
                </div>
                @endif
            </x-card>

            <div class="block lg:hidden space-y-3">
                @forelse ($clients as $client)
                <x-card class="space-y-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ $client->name }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $client->email }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $client->status ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                            {{ $client->status ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    @if ($client->phone)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[#94A3B8]">Phone</span>
                        <span class="text-[#94A3B8]">{{ $client->phone }}</span>
                    </div>
                    @endif
                    <div class="flex gap-2 pt-2 border-t border-[#232A36]">
                        <a href="{{ admin_route('workers.edit', $client) }}" class="flex-1 rounded-xl bg-[#3B82F6]/10 px-4 py-2.5 text-sm font-medium text-[#3B82F6] text-center hover:bg-[#3B82F6]/20">
                            Edit
                        </a>
                        <form method="POST" action="{{ admin_route('workers.toggle-status', $client) }}" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full rounded-xl bg-[#F59E0B]/10 px-4 py-2.5 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20">
                                {{ $client->status ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                    </div>
                </x-card>
                @empty
                <x-card class="py-12 text-center">
                    <p class="text-sm text-[#94A3B8]">No clients found.</p>
                </x-card>
                @endforelse
                @if ($clients->hasPages())
                <div class="pt-3">{{ $clients->links() }}</div>
                @endif
            </div>
        </div>
</x-layouts.app>