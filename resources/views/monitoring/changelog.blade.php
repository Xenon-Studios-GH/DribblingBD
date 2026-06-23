<x-layouts.app title="System Changelog">
    <div class="space-y-6" x-data="changelog()">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">System Changelog</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Track all security fixes, bug fixes, features, and refactors.</p>
            </div>
            <button @click="showCreate = !showCreate"
                class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                <i class="fa-solid fa-plus mr-1"></i> Add Entry
            </button>
        </div>

        {{-- Create Form --}}
        <x-card x-show="showCreate" x-cloak x-transition>
            <form method="POST" action="{{ admin_route('changelog.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Version</label>
                        <input type="text" name="version" value="1.0.0" required
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Category</label>
                        <select name="category" required
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <option value="security">Security</option>
                            <option value="bugfix">Bugfix</option>
                            <option value="feature">Feature</option>
                            <option value="refactor">Refactor</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Severity</label>
                        <select name="severity" required
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                            <option value="critical">Critical</option>
                            <option value="high">High</option>
                            <option value="medium">Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Title</label>
                        <input type="text" name="title" required maxlength="200"
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Description</label>
                    <textarea name="description" rows="2" required
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none"></textarea>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Files Affected (comma-separated)</label>
                        <input type="text" name="files_affected"
                            placeholder="app/Models/User.php, routes/web.php"
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Before State</label>
                        <input type="text" name="before_state"
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-[#94A3B8]">After State</label>
                        <input type="text" name="after_state"
                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showCreate = false"
                        class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333] hover:text-white transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        Save Entry
                    </button>
                </div>
            </form>
        </x-card>

        {{-- Filters --}}
        <x-card>
            <form method="GET" action="{{ admin_route('changelog.index') }}" class="flex flex-col md:flex-row items-stretch md:items-end gap-3 md:gap-4">
                <div class="flex-1 min-w-full md:min-w-[220px]">
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search title, description..."
                        class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Category</label>
                    <select name="category"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All</option>
                        <option value="security" {{ request('category') === 'security' ? 'selected' : '' }}>Security</option>
                        <option value="bugfix" {{ request('category') === 'bugfix' ? 'selected' : '' }}>Bugfix</option>
                        <option value="feature" {{ request('category') === 'feature' ? 'selected' : '' }}>Feature</option>
                        <option value="refactor" {{ request('category') === 'refactor' ? 'selected' : '' }}>Refactor</option>
                    </select>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-[#94A3B8]">Severity</label>
                    <select name="severity"
                        class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                        <option value="">All</option>
                        <option value="critical" {{ request('severity') === 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="high" {{ request('severity') === 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ request('severity') === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ request('severity') === 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit"
                        class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                        Filter
                    </button>
                    <a href="{{ admin_route('changelog.index') }}"
                        class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333] hover:text-white transition-colors">
                        Reset
                    </a>
                </div>
            </form>
        </x-card>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="rounded-xl border border-green-500/30 bg-green-500/10 px-4 py-3 text-sm text-green-400">
                {{ session('success') }}
            </div>
        @endif

        {{-- Table --}}
        <x-card padding="p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-[#232A36] text-[#94A3B8]">
                            <th class="px-5 py-3 font-medium">ID</th>
                            <th class="px-5 py-3 font-medium">Version</th>
                            <th class="px-5 py-3 font-medium">Category</th>
                            <th class="px-5 py-3 font-medium">Severity</th>
                            <th class="px-5 py-3 font-medium">Title</th>
                            <th class="px-5 py-3 font-medium">Files</th>
                            <th class="px-5 py-3 font-medium">Applied</th>
                            <th class="px-5 py-3 font-medium text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($changelogs as $log)
                            <tr class="border-b border-[#232A36] hover:bg-[#1C2333] transition-colors"
                                x-data="{ edit: false }">
                                <td class="px-5 py-3 text-[#94A3B8]">{{ $log->id }}</td>
                                <td class="px-5 py-3">
                                    <span class="rounded-lg bg-[#232A36] px-2 py-0.5 text-xs font-mono text-[#E6EDF3]">{{ $log->version }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $catColors = ['security' => 'red', 'bugfix' => 'amber', 'feature' => 'blue', 'refactor' => 'slate'];
                                        $color = $catColors[$log->category] ?? 'slate';
                                    @endphp
                                    <span class="rounded-lg bg-{{ $color }}-500/15 px-2 py-0.5 text-xs font-medium text-{{ $color }}-400">{{ ucfirst($log->category) }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $sevColors = ['critical' => 'red', 'high' => 'orange', 'medium' => 'yellow', 'low' => 'green'];
                                        $svColor = $sevColors[$log->severity] ?? 'slate';
                                    @endphp
                                    <span class="rounded-lg bg-{{ $svColor }}-500/15 px-2 py-0.5 text-xs font-medium text-{{ $svColor }}-400">{{ ucfirst($log->severity) }}</span>
                                </td>
                                <td class="px-5 py-3 text-[#E6EDF3] font-medium max-w-[300px] truncate">{{ $log->title }}</td>
                                <td class="px-5 py-3 text-[#94A3B8] text-xs">{{ count(json_decode($log->files_affected ?? '[]', true)) }} file(s)</td>
                                <td class="px-5 py-3 text-[#94A3B8] text-xs">{{ $log->applied_at?->format('M d, Y H:i') ?? '-' }}</td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <button @click="edit = !edit"
                                            class="rounded-lg p-1.5 text-[#94A3B8] hover:bg-[#232A36] hover:text-[#E6EDF3] transition-colors"
                                            title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </button>
                                        <form method="POST" action="{{ admin_route('changelog.destroy', $log->id) }}"
                                            onsubmit="return confirm('Delete this entry?')"
                                            class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="rounded-lg p-1.5 text-[#94A3B8] hover:bg-red-500/20 hover:text-red-400 transition-colors"
                                                title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Row --}}
                            <tr x-show="edit" x-cloak x-transition class="bg-[#0D1017]">
                                <td colspan="8" class="px-5 py-4">
                                    <form method="POST" action="{{ admin_route('changelog.update', $log->id) }}" class="space-y-3">
                                        @csrf @method('PUT')
                                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                                            <input type="text" name="version" value="{{ $log->version }}" required
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                            <select name="category" required
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                                <option value="security" {{ $log->category === 'security' ? 'selected' : '' }}>Security</option>
                                                <option value="bugfix" {{ $log->category === 'bugfix' ? 'selected' : '' }}>Bugfix</option>
                                                <option value="feature" {{ $log->category === 'feature' ? 'selected' : '' }}>Feature</option>
                                                <option value="refactor" {{ $log->category === 'refactor' ? 'selected' : '' }}>Refactor</option>
                                            </select>
                                            <select name="severity" required
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                                <option value="critical" {{ $log->severity === 'critical' ? 'selected' : '' }}>Critical</option>
                                                <option value="high" {{ $log->severity === 'high' ? 'selected' : '' }}>High</option>
                                                <option value="medium" {{ $log->severity === 'medium' ? 'selected' : '' }}>Medium</option>
                                                <option value="low" {{ $log->severity === 'low' ? 'selected' : '' }}>Low</option>
                                            </select>
                                            <input type="text" name="title" value="{{ $log->title }}" required maxlength="200"
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">
                                        </div>
                                        <textarea name="description" rows="2" required
                                            class="w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] focus:border-[#3B82F6] focus:outline-none">{{ $log->description }}</textarea>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                            <input type="text" name="files_affected"
                                                value="{{ implode(', ', json_decode($log->files_affected ?? '[]', true)) }}"
                                                placeholder="Files (comma-separated)"
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                                            <input type="text" name="before_state" value="{{ $log->before_state }}"
                                                placeholder="Before state"
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                                            <input type="text" name="after_state" value="{{ $log->after_state }}"
                                                placeholder="After state"
                                                class="rounded-xl border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3] placeholder-[#94A3B8] focus:border-[#3B82F6] focus:outline-none">
                                        </div>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="edit = false"
                                                class="rounded-xl border border-[#232A36] px-3 py-1.5 text-xs text-[#94A3B8] hover:bg-[#1C2333] hover:text-white transition-colors">
                                                Cancel
                                            </button>
                                            <button type="submit"
                                                class="rounded-xl bg-[#3B82F6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#2563EB] transition-colors">
                                                Update
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-[#94A3B8]">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-[#232A36]"></i>
                                    No changelog entries found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($changelogs->hasPages())
                <div class="border-t border-[#232A36] px-5 py-3">
                    {{ $changelogs->links() }}
                </div>
            @endif
        </x-card>
    </div>

    <script>
        function changelog() {
            return {
                showCreate: false,
            }
        }
    </script>
</x-layouts.app>
