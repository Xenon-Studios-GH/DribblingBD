<x-layouts.app title="{{ $project->name }}">
    <div x-data="projectEditor()" x-init="init()" class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <input type="text" x-model="name"
                    class="text-2xl font-bold text-[#E6EDF3] bg-transparent border-b-2 border-transparent focus:border-[#3B82F6] focus:outline-none px-1 -ml-1">
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ $project->status === 'active' ? 'bg-[#22C55E]/10 text-[#22C55E]' : '' }}
                    {{ $project->status === 'completed' ? 'bg-[#3B82F6]/10 text-[#3B82F6]' : '' }}
                    {{ $project->status === 'archived' ? 'bg-[#94A3B8]/10 text-[#94A3B8]' : '' }}">
                    <span x-text="status.charAt(0).toUpperCase() + status.slice(1)"></span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span x-show="saved" x-cloak class="text-xs text-[#22C55E] flex items-center gap-1">
                    <i class="fas fa-check"></i> Saved
                </span>
                <span x-show="!saved && dirty" x-cloak class="text-xs text-[#F59E0B] flex items-center gap-1">
                    <i class="fas fa-circle-notch fa-spin"></i> Saving...
                </span>
                <button @click="save()" :disabled="!dirty"
                    class="rounded-xl px-4 py-2 text-sm font-medium transition-colors"
                    :class="dirty ? 'bg-[#3B82F6] text-white hover:bg-[#2563EB]' : 'bg-[#232A36] text-[#64748B] cursor-not-allowed'">
                    <i class="fas fa-save mr-1"></i> Save
                </button>
                <a href="{{ admin_route('finance.projects') }}" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">Back</a>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card>
                <p class="text-sm text-[#94A3B8]">Budget</p>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-xs text-[#64748B]">৳</span>
                    <input type="number" step="0.01" min="0" x-model="budget"
                        class="text-2xl font-bold text-[#E6EDF3] bg-transparent border-b border-transparent focus:border-[#3B82F6] focus:outline-none w-full">
                </div>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Total Spent</p>
                <p class="text-2xl font-bold text-[#EF4444] mt-1">৳{{ number_format($totalExpense, 2) }}</p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Remaining</p>
                <p class="text-2xl font-bold mt-1" :class="budget - {{ $totalExpense }} >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]'">
                    ৳<span x-text="(parseFloat(budget || 0) - {{ $totalExpense }}).toFixed(2)"></span>
                </p>
            </x-card>
            <x-card>
                <p class="text-sm text-[#94A3B8]">Total Income</p>
                <p class="text-2xl font-bold text-[#22C55E] mt-1">৳{{ number_format($totalIncome, 2) }}</p>
            </x-card>
        </div>

        {{-- Budget Utilization Bar --}}
        <x-card>
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm font-medium text-[#94A3B8]">Budget Utilization</h2>
                <span class="text-sm font-medium text-[#E6EDF3]">
                    ৳<span x-text="parseFloat(budget || 0) > 0 ? '{{ $totalExpense }}' : '0'"></span> / ৳<span x-text="parseFloat(budget || 0).toFixed(2)"></span>
                    (<span x-text="parseFloat(budget || 0) > 0 ? ({{ $totalExpense }} / parseFloat(budget) * 100).toFixed(1) : '0'"></span>%)
                </span>
            </div>
            <div class="h-3 rounded-full bg-[#232A36] overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500"
                    :style="'width: ' + Math.min(parseFloat(budget || 0) > 0 ? ({{ $totalExpense }} / parseFloat(budget) * 100) : 0, 100) + '%'"
                    :class="(parseFloat(budget || 0) > 0 ? ({{ $totalExpense }} / parseFloat(budget) * 100) : 0) > 90 ? 'bg-[#EF4444]' : (parseFloat(budget || 0) > 0 ? ({{ $totalExpense }} / parseFloat(budget) * 100) : 0) > 75 ? 'bg-[#F59E0B]' : 'bg-[#22C55E]'">
                </div>
            </div>
        </x-card>

        {{-- Details --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card>
                <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Project Details</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-[#94A3B8] mb-1">Status</label>
                        <select x-model="status"
                            class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-[#94A3B8] mb-1">Start Date</label>
                            <input type="date" x-model="startDate"
                                class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-[#94A3B8] mb-1">End Date</label>
                            <input type="date" x-model="endDate"
                                class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-[#94A3B8] mb-1">Description / Notes</label>
                        <textarea x-model="description" rows="4"
                            class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3] resize-y"></textarea>
                    </div>
                    <div class="text-xs text-[#64748B] space-y-1">
                        <p>Created by: {{ $project->creator?->name ?? 'Unknown' }} on {{ $project->created_at->format('M d, Y h:i A') }}</p>
                        @if($project->updated_by)
                        <p>Last edited by: {{ $project->updater?->name ?? 'Unknown' }}</p>
                        @endif
                    </div>
                </div>
            </x-card>

            {{-- Recent Transactions --}}
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Transactions</h2>
                    <a href="{{ admin_route('finance.transactions.create') }}" class="text-sm text-[#3B82F6] hover:underline">+ Add</a>
                </div>
                <div class="space-y-3 max-h-[400px] overflow-y-auto">
                    @forelse($project->transactions->sortByDesc('date') as $t)
                    <div class="flex items-center justify-between py-2 border-b border-[#232A36] last:border-0">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ $t->description ?: 'No description' }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ $t->date->format('M d, Y') }} · {{ $t->category?->name ?? 'Uncategorized' }} · {{ $t->creator?->name ?? '—' }}</p>
                        </div>
                        <span class="text-sm font-semibold {{ $t->type === 'income' ? 'text-[#22C55E]' : 'text-[#EF4444]' }} ml-3">
                            {{ $t->type === 'income' ? '+' : '-' }}৳{{ number_format($t->amount, 2) }}
                        </span>
                    </div>
                    @empty
                    <p class="text-sm text-[#94A3B8] text-center py-8">No transactions for this project yet.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    <script>
        function projectEditor() {
            return {
                name: '{{ $project->name }}',
                budget: '{{ $project->budget ?? '' }}',
                status: '{{ $project->status }}',
                startDate: '{{ $project->start_date?->format('Y-m-d') ?? '' }}',
                endDate: '{{ $project->end_date?->format('Y-m-d') ?? '' }}',
                description: '{{ addslashes($project->description ?? '') }}',
                saved: true,
                dirty: false,
                saveTimer: null,
                init() {
                    this.$watch('name', () => this.markDirty());
                    this.$watch('budget', () => this.markDirty());
                    this.$watch('status', () => this.markDirty());
                    this.$watch('startDate', () => this.markDirty());
                    this.$watch('endDate', () => this.markDirty());
                    this.$watch('description', () => this.markDirty());
                },
                markDirty() {
                    this.saved = false;
                    this.dirty = true;
                    clearTimeout(this.saveTimer);
                    this.saveTimer = setTimeout(() => this.save(), 2000);
                },
                save() {
                    if (!this.dirty) return;
                    this.dirty = false;
                    fetch('{{ admin_route('finance.projects.quick', ['project' => $project]) }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            name: this.name,
                            budget: this.budget || null,
                            status: this.status,
                            start_date: this.startDate || null,
                            end_date: this.endDate || null,
                            description: this.description,
                        }),
                    })
                    .then(r => r.json())
                    .then(d => {
                        if (d.success) {
                            this.saved = true;
                        }
                    })
                    .catch(() => {
                        this.dirty = true;
                    });
                },
            };
        }
    </script>
</x-layouts.app>
