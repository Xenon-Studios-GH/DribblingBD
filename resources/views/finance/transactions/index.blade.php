<x-layouts.app title="Transactions">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Transactions</h1>
            <a href="{{ admin_route('finance.transactions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                <i class="fas fa-plus"></i> Add Transaction
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap gap-3">
            <select name="type" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                <option value="">All Types</option>
                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>Income</option>
                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
            </select>
            <select name="category_id" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="project_id" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                <option value="">All Projects</option>
                @foreach($projects as $proj)
                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from', now()->subYear()->format('Y-m-d')) }}" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
            <input type="date" name="date_to" value="{{ request('date_to', now()->format('Y-m-d')) }}" class="rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
            <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white">Filter</button>
            <a href="{{ admin_route('finance.transactions') }}" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8]">Reset</a>
        </form>

        {{-- Table --}}
        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                        <th class="pb-3 font-medium">Date</th>
                        <th class="pb-3 font-medium">Type</th>
                        <th class="pb-3 font-medium">Category</th>
                        <th class="pb-3 font-medium">Project</th>
                        <th class="pb-3 font-medium text-right">Amount</th>
                        <th class="pb-3 font-medium">Description</th>
                        <th class="pb-3 font-medium">By</th>
                        <th class="pb-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                        <td class="py-3 text-[#E6EDF3]">{{ $t->date->format('M d, Y') }}</td>
                        <td class="py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $t->type === 'income' ? 'bg-[#22C55E]/10 text-[#22C55E]' : 'bg-[#EF4444]/10 text-[#EF4444]' }}">
                                {{ ucfirst($t->type) }}
                            </span>
                        </td>
                        <td class="py-3 text-[#94A3B8]">{{ $t->category?->name ?? '—' }}</td>
                        <td class="py-3 text-[#94A3B8]">{{ $t->project?->name ?? '—' }}</td>
                        <td class="py-3 text-right font-semibold {{ $t->type === 'income' ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">
                            ৳{{ number_format($t->amount, 2) }}
                        </td>
                        <td class="py-3 text-[#94A3B8] max-w-[200px] truncate">{{ $t->description ?: '—' }}</td>
                        <td class="py-3 text-[#94A3B8]">{{ $t->creator?->name ?? '—' }}</td>
                        <td class="py-3 text-right">
                            <a href="{{ admin_route('finance.transactions.edit', $t) }}" class="text-[#3B82F6] hover:underline text-xs">Edit</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-[#94A3B8]">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{ $transactions->links() }}
    </div>
</x-layouts.app>
