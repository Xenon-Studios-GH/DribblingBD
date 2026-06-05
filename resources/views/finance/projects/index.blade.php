<x-layouts.app title="Finance Projects">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Projects</h1>
            <button @click="$dispatch('open-project-modal', {})" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white hover:bg-[#2563EB]">
                <i class="fas fa-plus"></i> New Project
            </button>
        </div>

        <x-card class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                        <th class="pb-3 font-medium">Name</th>
                        <th class="pb-3 font-medium">Budget</th>
                        <th class="pb-3 font-medium">Spent</th>
                        <th class="pb-3 font-medium">Remaining</th>
                        <th class="pb-3 font-medium">Status</th>
                        <th class="pb-3 font-medium">Dates</th>
                        <th class="pb-3 font-medium text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $p)
                    @php
                        $spent = \App\Models\FinanceTransaction::where('project_id', $p->id)->expense()->sum('amount');
                        $remaining = ($p->budget ?? 0) - $spent;
                    @endphp
                    <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                        <td class="py-3 font-medium text-[#E6EDF3]">{{ $p->name }}</td>
                        <td class="py-3 text-[#94A3B8]">{{ $p->budget ? '৳'.number_format($p->budget, 2) : '—' }}</td>
                        <td class="py-3 text-[#EF4444]">৳{{ number_format($spent, 2) }}</td>
                        <td class="py-3 {{ $remaining >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">৳{{ number_format($remaining, 2) }}</td>
                        <td class="py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $p->status === 'active' ? 'bg-[#22C55E]/10 text-[#22C55E]' : '' }}
                                {{ $p->status === 'completed' ? 'bg-[#3B82F6]/10 text-[#3B82F6]' : '' }}
                                {{ $p->status === 'archived' ? 'bg-[#94A3B8]/10 text-[#94A3B8]' : '' }}">
                                {{ ucfirst($p->status) }}
                            </span>
                        </td>
                        <td class="py-3 text-[#94A3B8] text-xs">
                            @if($p->start_date) {{ $p->start_date->format('M d, Y') }} @endif
                            @if($p->end_date) — {{ $p->end_date->format('M d, Y') }} @endif
                        </td>
                        <td class="py-3 text-right">
                            <button @click="$dispatch('open-project-modal', {id: {{ $p->id }}, name: '{{ $p->name }}', budget: '{{ $p->budget }}', status: '{{ $p->status }}', start_date: '{{ $p->start_date?->format('Y-m-d') }}', end_date: '{{ $p->end_date?->format('Y-m-d') }}', description: '{{ addslashes($p->description ?? '') }}'})" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-[#94A3B8]">No projects yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{ $projects->links() }}
    </div>

    {{-- Project Modal --}}
    <div x-data="projectModal()" @open-project-modal.window="open($event.detail)" x-show="isOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/50" @click="isOpen = false"></div>
        <div class="relative bg-[#161B22] border border-[#232A36] rounded-xl p-6 w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold text-[#E6EDF3] mb-4" x-text="editingId ? 'Edit Project' : 'New Project'"></h3>
            <form :action="editingId ? '{{ admin_route('finance.projects.update', '') }}/' + editingId : '{{ admin_route('finance.projects.store') }}'" method="POST">
                @csrf
                <template x-if="editingId">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Name</label>
                        <input type="text" name="name" x-model="projectName" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Budget (BDT)</label>
                        <input type="number" step="0.01" min="0" name="budget" x-model="projectBudget" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Status</label>
                        <select name="status" x-model="projectStatus" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Start Date</label>
                            <input type="date" name="start_date" x-model="projectStartDate" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-[#E6EDF3] mb-2">End Date</label>
                            <input type="date" name="end_date" x-model="projectEndDate" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[#E6EDF3] mb-2">Description</label>
                        <textarea name="description" x-model="projectDescription" rows="3" class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" @click="isOpen = false" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8]">Cancel</button>
                    <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white" x-text="editingId ? 'Update' : 'Create'"></button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function projectModal() {
            return {
                isOpen: false,
                editingId: null,
                projectName: '',
                projectBudget: '',
                projectStatus: 'active',
                projectStartDate: '',
                projectEndDate: '',
                projectDescription: '',
                open(detail) {
                    this.editingId = detail.id || null;
                    this.projectName = detail.name || '';
                    this.projectBudget = detail.budget || '';
                    this.projectStatus = detail.status || 'active';
                    this.projectStartDate = detail.start_date || '';
                    this.projectEndDate = detail.end_date || '';
                    this.projectDescription = detail.description || '';
                    this.isOpen = true;
                },
            };
        }
    </script>
</x-layouts.app>
