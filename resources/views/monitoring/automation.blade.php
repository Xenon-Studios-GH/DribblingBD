<x-layouts.app title="Automation">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Automation</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Monitor and control everything the system does automatically — live page refreshes, background cleanup tasks, stock checks, and more.</p>
        </div>

        {{-- Row 1: Overview Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Scheduled Tasks</span>
                    <i class="fas fa-clock text-[#3B82F6] text-sm"></i>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ $schedulerStats['count'] }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">{{ $schedulerStats['frequent'] }} frequent · {{ $schedulerStats['hourly'] }} hourly · {{ $schedulerStats['slow'] }} daily</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Live Page Refreshes</span>
                    <i class="fas fa-sync text-[#22C55E] text-sm"></i>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ count($clientPolls) }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Auto-refresh in the browser</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Auto SEO</span>
                    <i class="fas fa-search text-[#A855F7] text-sm"></i>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ number_format($autoSeoCount) }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Auto-generated entries</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Audit Last Run</span>
                    <span class="flex h-2 w-2 rounded-full {{ $auditLogs->isNotEmpty() && $auditLogs->first()['issues'] === 0 ? 'bg-[#22C55E]' : 'bg-[#F59E0B]' }}"></span>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ $auditLogs->isNotEmpty() ? $auditLogs->first()['ran_at']->diffForHumans() : 'Never' }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Consistency check</p>
            </div>
        </div>

        {{-- Row 2: Interactive Polling Table + Scheduler --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Interactive Polling Table (2 cols) --}}
            <div class="lg:col-span-2 rounded-xl border border-[#232A36] bg-[#161B22] p-4" x-data="pollingTable()" x-init="init()">
                <div class="flex items-center justify-between mb-1">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-sync text-[#22C55E] mr-2"></i>Live Page Refreshes</h2>
                    <div class="flex items-center gap-2">
                        <button @click="resetAll()" class="rounded-lg bg-[#232A36] px-3 py-1.5 text-[10px] font-medium text-[#94A3B8] hover:text-[#E6EDF3] transition-colors">
                            <i class="fas fa-redo mr-1"></i> Reset All
                        </button>
                        <button @click="pollAllNow()" class="rounded-lg bg-[#22C55E]/20 px-3 py-1.5 text-[10px] font-medium text-[#22C55E] hover:bg-[#22C55E]/30 transition-colors">
                            <i class="fas fa-play mr-1"></i> Run All Now
                        </button>
                    </div>
                </div>
                <p class="text-[10px] text-[#6B7280] mb-3">These auto-refresh data on different pages so you always see the latest information. Turn off any you don't need, or change how often they refresh.</p>

                {{-- Table Header --}}
                <div class="hidden md:grid md:grid-cols-12 gap-2 px-3 py-2 text-[10px] font-medium text-[#94A3B8] uppercase tracking-wider border-b border-[#232A36]">
                    <div class="col-span-1"></div>
                    <div class="col-span-3">Name</div>
                    <div class="col-span-2">Interval (sec)</div>
                    <div class="col-span-2">Location</div>
                    <div class="col-span-2">Last Run</div>
                    <div class="col-span-1">Runs</div>
                    <div class="col-span-1">On/Off</div>
                </div>

                {{-- Table Rows --}}
                <template x-for="(poll, index) in polls" :key="poll.key">
                    <div>
                        {{-- Main Row --}}
                        <div class="grid grid-cols-12 gap-2 items-center px-3 py-2.5 rounded-lg hover:bg-[#1C2333] transition-colors cursor-pointer border-b border-[#232A36]/50"
                             @click="toggleExpand(poll.key)">

                            {{-- Expand Icon --}}
                            <div class="col-span-1">
                                <i class="fas transition-transform duration-200 text-[10px] text-[#6B7280]"
                                   :class="expanded === poll.key ? 'fa-chevron-down rotate-0' : 'fa-chevron-right'"></i>
                            </div>

                            {{-- Name --}}
                            <div class="col-span-3">
                                <p class="text-xs font-medium text-[#E6EDF3]" x-text="poll.name"></p>
                                <p class="text-[10px] text-[#6B7280] md:hidden" x-text="poll.page"></p>
                            </div>

                            {{-- Interval (editable) --}}
                            <div class="col-span-2" @click.stop>
                                <div class="flex items-center gap-1">
                                    <input type="number" min="5" max="300" step="5"
                                           x-model.number="poll.intervalSec"
                                           @change="updateInterval(poll)"
                                           class="w-14 rounded border border-[#232A36] bg-[#0F1117] px-1.5 py-0.5 text-[11px] text-[#E6EDF3] text-center focus:border-[#3B82F6] focus:outline-none">
                                    <span class="text-[10px] text-[#6B7280]">sec</span>
                                </div>
                            </div>

                            {{-- Page --}}
                            <div class="col-span-2 hidden md:block">
                        <span class="text-[11px] text-[#94A3B8]" x-text="poll.page"></span>
                    </div>

                            {{-- Last Run --}}
                            <div class="col-span-2">
                                <span class="text-[11px] text-[#94A3B8]" x-text="poll.lastRun || 'Never'"></span>
                            </div>

                            {{-- Run Count --}}
                            <div class="col-span-1">
                                <span class="text-[11px] text-[#E6EDF3] font-medium" x-text="poll.runCount"></span>
                            </div>

                            {{-- Toggle --}}
                            <div class="col-span-1" @click.stop>
                                <button @click="togglePoll(poll)"
                                        class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors duration-200"
                                        :class="poll.isActive ? 'bg-[#22C55E]' : 'bg-[#374151]'">
                                    <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white transition-transform duration-200"
                                          :class="poll.isActive ? 'translate-x-[18px]' : 'translate-x-[2px]'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Expanded Details --}}
                        <div x-show="expanded === poll.key" x-collapse x-cloak
                             class="ml-6 mr-3 mb-2 rounded-lg bg-[#0F1117] border border-[#232A36] p-3">
                            <p class="text-[11px] text-[#94A3B8] mb-2" x-text="poll.description"></p>
                            <div class="grid grid-cols-2 gap-3 text-[10px]">
                                <div>
                                    <span class="text-[#6B7280]">Interval (ms)</span>
                                    <p class="text-[#E6EDF3] font-medium" x-text="poll.intervalMs"></p>
                                </div>
                                <div>
                                    <span class="text-[#6B7280]">Default</span>
                                    <p class="text-[#E6EDF3] font-medium" x-text="poll.defaultInterval + ' sec'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Empty State --}}
                <template x-if="polls.length === 0">
                    <div class="py-6 text-center">
                        <p class="text-xs text-[#6B7280]">No polls registered on this page.</p>
                        <p class="text-[10px] text-[#6B7280] mt-1">Navigate to Dashboard, Orders, or Stock Management to activate polls.</p>
                    </div>
                </template>
            </div>

            {{-- Scheduler (1 col) --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-clock text-[#3B82F6] mr-2"></i>Scheduled Tasks</h2>
                    <span class="text-xs text-[#94A3B8]">{{ count($schedulerTasks) }} tasks</span>
                </div>
                <p class="text-[10px] text-[#6B7280] mb-3">These tasks run automatically in the background to keep the system clean and healthy. Click <i class="fas fa-play text-[8px]"></i> to run any task immediately.</p>

                {{-- Table Header --}}
                <div class="hidden md:grid md:grid-cols-12 gap-2 px-3 py-2 text-[10px] font-medium text-[#94A3B8] uppercase tracking-wider border-b border-[#232A36]">
                    <div class="col-span-1"></div>
                    <div class="col-span-4">Task</div>
                    <div class="col-span-2">Schedule</div>
                    <div class="col-span-2">Last Run</div>
                    <div class="col-span-3">Run Now</div>
                </div>

                {{-- Table Rows --}}
                <div class="space-y-0">
                    @forelse ($schedulerTasks as $task)
                    <div x-data="{ expanded: false }">
                        {{-- Main Row --}}
                        <div class="grid grid-cols-12 gap-2 items-center px-3 py-2.5 rounded-lg hover:bg-[#1C2333] transition-colors cursor-pointer border-b border-[#232A36]/50"
                             @click="expanded = !expanded">
                            <div class="col-span-1">
                                <i class="fas transition-transform duration-200 text-[10px] text-[#6B7280]"
                                   :class="expanded ? 'fa-chevron-down' : 'fa-chevron-right'"></i>
                            </div>
                            <div class="col-span-4">
                                <p class="text-xs font-medium text-[#E6EDF3] truncate">{{ $task['name'] }}</p>
                                <p class="text-[10px] text-[#6B7280] md:hidden">{{ $task['frequency'] }}</p>
                            </div>
                            <div class="col-span-2">
                                <span class="rounded-full px-2.5 py-0.5 text-[10px] font-medium
                                    {{ $task['type'] === 'frequent' ? 'bg-[#22C55E]/20 text-[#22C55E]' : '' }}
                                    {{ $task['type'] === 'hourly' ? 'bg-[#3B82F6]/20 text-[#3B82F6]' : '' }}
                                    {{ $task['type'] === 'slow' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : '' }}
                                ">{{ $task['frequency'] }}</span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[11px] text-[#94A3B8]">{{ $task['last_run_at']['formatted'] ?? 'Never' }}</span>
                            </div>
                            <div class="col-span-3" @click.stop>
                                <form action="{{ admin_route('monitoring.run-task') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="command" value="{{ $task['command_signature'] }}">
                                    <input type="hidden" name="task_name" value="{{ $task['name'] }}">
                                    <button type="submit" class="rounded-lg bg-[#3B82F6]/20 px-3 py-1 text-[10px] font-medium text-[#3B82F6] hover:bg-[#3B82F6]/30 transition-colors whitespace-nowrap">
                                        <i class="fas fa-play mr-0.5"></i> Run Now
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Expanded Details --}}
                        <div x-show="expanded" x-collapse x-cloak
                             class="ml-6 mr-3 mb-2 rounded-lg bg-[#0F1117] border border-[#232A36] p-3">
                            <p class="text-[11px] text-[#94A3B8] mb-2">{{ $task['description'] }}</p>
                            @if ($task['last_run_at'])
                            <div class="text-[10px] text-[#6B7280]">
                                <i class="fas fa-check-circle text-[#22C55E] mr-1"></i>Last completed {{ $task['last_run_at']['diff'] ?? 'N/A' }} · Run {{ $task['run_count'] }} time{{ $task['run_count'] !== 1 ? 's' : '' }}
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="py-6 text-center">
                        <p class="text-xs text-[#6B7280]">No scheduled tasks found.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Row 3: Stock Auto-Checker + Audit History --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Stock Auto-Checker --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-cubes text-[#F59E0B] mr-2"></i>Stock Auto-Checker</h2>
                    <span class="text-xs text-[#94A3B8]">Every 30s · All orders</span>
                </div>
                <div class="flex items-center gap-3 mb-3 rounded-lg bg-[#1C2333] px-3 py-2">
                    <i class="fas fa-history text-[#3B82F6] text-xs"></i>
                    <div>
                        <p class="text-xs text-[#E6EDF3]">{{ $autoRestoredCount }} auto-restored orders</p>
                        <p class="text-[10px] text-[#94A3B8]">{{ $stockCheckLogs->count() }} recent actions logged</p>
                    </div>
                </div>
                <div class="space-y-1.5">
                    <p class="text-[10px] font-medium text-[#94A3B8] uppercase tracking-wider">Recent Stock Alerts</p>
                    @forelse ($stockCheckLogs as $log)
                    <div class="flex items-center gap-2 rounded-lg bg-[#0F1117] px-2.5 py-1.5">
                        @if (str_contains($log['action'], 'Auto-Restored'))
                        <i class="fas fa-arrow-up text-[#22C55E] text-[10px]"></i>
                        @else
                        <i class="fas fa-arrow-down text-[#EF4444] text-[10px]"></i>
                        @endif
                        <span class="text-[11px] text-[#94A3B8] truncate flex-1">{{ $log['action'] }}</span>
                        <span class="text-[10px] text-[#6B7280]">{{ $log['ran_at']->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-[#94A3B8] text-center py-2">No stock alerts yet.</p>
                    @endforelse
                </div>
            </div>

            {{-- Audit History --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-shield-alt text-[#22C55E] mr-2"></i>Audit History</h2>
                    <span class="text-xs text-[#94A3B8]">Last 10 checks</span>
                </div>
                <p class="text-[10px] text-[#6B7280] mb-3">The system regularly scans itself for problems and fixes them. Each check looks for missing records, wrong data, or broken links.</p>
                <div class="space-y-2 max-h-[300px] overflow-y-auto">
                    @forelse ($auditLogs as $log)
                    <div class="flex items-start justify-between rounded-lg bg-[#1C2333] p-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                @if ($log['issues'] === 0)
                                <i class="fas fa-check-circle text-[#22C55E] text-xs"></i>
                                @elseif ($log['fixed'] > 0)
                                <i class="fas fa-wrench text-[#F59E0B] text-xs"></i>
                                @else
                                <i class="fas fa-exclamation-circle text-[#EF4444] text-xs"></i>
                                @endif
                                <p class="text-xs font-medium text-[#E6EDF3] truncate">{{ $log['summary'] }}</p>
                            </div>
                            @if ($log['checks'])
                            <div class="flex flex-wrap gap-1 mt-1.5">
                                @foreach ($log['checks'] as $check)
                                <span class="rounded-md bg-[#0F1117] px-1.5 py-0.5 text-[10px] text-[#94A3B8]">{{ $check }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        <span class="shrink-0 text-[10px] text-[#94A3B8] ml-2">{{ $log['ran_at']->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-[#94A3B8] text-center py-4">No audit runs yet. Runs every 5 minutes.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Row 4: Model Observers + Orders Breakdown --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Model Observers --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-eye text-[#A855F7] mr-2"></i>Model Observers (Auto SEO)</h2>
                    <span class="text-xs text-[#94A3B8]">Event-driven</span>
                </div>
                <div class="space-y-2">
                    <div class="rounded-lg bg-[#1C2333] p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-box text-[#EC4899] text-xs"></i>
                                <span class="text-xs text-[#E6EDF3]">Product Observer</span>
                            </div>
                            <span class="text-xs text-[#22C55E]">{{ $observerStats['auto_products'] }}/{{ $observerStats['total_products'] }}</span>
                        </div>
                        <p class="text-[10px] text-[#94A3B8] mt-1">Auto-generates SEO on create/update</p>
                    </div>
                    <div class="rounded-lg bg-[#1C2333] p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-folder text-[#3B82F6] text-xs"></i>
                                <span class="text-xs text-[#E6EDF3]">Category Observer</span>
                            </div>
                            <span class="text-xs text-[#94A3B8]">Active</span>
                        </div>
                        <p class="text-[10px] text-[#94A3B8] mt-1">Auto-generates SEO on create/update</p>
                    </div>
                    <div class="rounded-lg bg-[#1C2333] p-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-globe text-[#22C55E] text-xs"></i>
                                <span class="text-xs text-[#E6EDF3]">Project Observer</span>
                            </div>
                            <span class="text-xs text-[#94A3B8]">Active</span>
                        </div>
                        <p class="text-[10px] text-[#94A3B8] mt-1">Auto-generates SEO on create/update</p>
                    </div>
                    <div class="mt-2 rounded-lg bg-[#0F1117] px-3 py-2">
                        <div class="flex items-center justify-between text-[10px]">
                            <span class="text-[#94A3B8]">Total SEO records</span>
                            <span class="text-[#E6EDF3] font-medium">{{ number_format($observerStats['total_seo']) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders Breakdown --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-receipt text-[#F59E0B] mr-2"></i>Orders Breakdown</h2>
                    <span class="text-xs text-[#94A3B8]">Count / Total / Pending</span>
                </div>
                @php
                    $statusColors = [
                        'pending' => '#F59E0B', 'on_hold' => '#3B82F6', 'packed' => '#EC4899',
                        'picked' => '#14B8A6', 'delivered' => '#22C55E', 'return' => '#EF4444',
                        'refund' => '#A855F7', 'out_of_stock' => '#6B7280', 'draft' => '#6B7280',
                    ];
                    $statusLabels = [
                        'pending' => 'Pending', 'on_hold' => 'On Hold', 'packed' => 'Packed',
                        'picked' => 'Picked', 'delivered' => 'Delivered', 'return' => 'Return',
                        'refund' => 'Refund', 'out_of_stock' => 'Out of Stock', 'draft' => 'Draft',
                    ];
                @endphp
                <div class="space-y-1.5">
                    @foreach (['pending', 'on_hold', 'packed', 'picked', 'delivered', 'return', 'refund', 'out_of_stock', 'draft'] as $status)
                    @php $row = $ordersByStatus->get($status); @endphp
                    <div class="flex items-center gap-3 rounded-lg bg-[#1C2333] px-3 py-2">
                        <span class="flex h-2.5 w-2.5 shrink-0 rounded-full" style="background-color: {{ $statusColors[$status] }}"></span>
                        <span class="text-xs text-[#94A3B8] w-24">{{ $statusLabels[$status] }}</span>
                        <span class="text-xs font-medium text-[#E6EDF3] w-8 text-right">{{ $row ? $row->count : 0 }}</span>
                        <span class="text-xs text-[#22C55E] w-24 text-right">৳{{ number_format($row ? $row->total : 0, 0) }}</span>
                        <span class="text-xs text-[#F59E0B] w-24 text-right">৳{{ number_format($row ? $row->pending : 0, 0) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Row 5: Quick Actions + Log Files --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- On-Demand Actions --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-bolt text-[#F59E0B] mr-2"></i>On-Demand Automation</h2>
                    <span class="text-xs text-[#94A3B8]">Manual triggers</span>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form action="{{ admin_route('monitoring.run-audit') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg border border-[#232A36] px-4 py-2 text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                            <i class="fas fa-play mr-1.5"></i> Run Audit Now
                        </button>
                    </form>
                    <form action="{{ admin_route('seo.auto-generate') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="rounded-lg border border-[#232A36] px-4 py-2 text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                            <i class="fas fa-magic mr-1.5"></i> Auto-Generate SEO
                        </button>
                    </form>
                    <a href="{{ admin_route('monitoring.index', ['tab' => 'automation']) }}" class="rounded-lg border border-[#232A36] px-4 py-2 text-sm text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                        <i class="fas fa-history mr-1.5"></i> Audit Logs
                    </a>
                </div>

                <div class="mt-4 pt-3 border-t border-[#232A36]">
                    <h3 class="text-xs font-medium text-[#94A3B8] mb-2">Pending Confirmations</h3>
                    <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2.5">
                        <span class="text-xs text-[#94A3B8]">Orders awaiting finance confirmation</span>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-[#F59E0B]">{{ $pendingTransactions }}</span>
                            <span class="text-xs text-[#22C55E]">৳{{ number_format($pendingAmount, 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Log Files --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-file-alt text-[#3B82F6] mr-2"></i>System Logs</h2>
                    <span class="text-xs text-[#94A3B8]">storage/logs</span>
                </div>
                <div class="space-y-2">
                    @foreach (['audit' => 'Audit Log', 'cleanup' => 'Cleanup Log', 'laravel' => 'Laravel Log'] as $key => $label)
                    @php $file = $logFiles[$key]; @endphp
                    <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2.5">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-alt text-xs {{ $file['exists'] ? 'text-[#3B82F6]' : 'text-[#6B7280]' }}"></i>
                            <span class="text-xs font-medium text-[#E6EDF3]">{{ $label }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-[#94A3B8]">
                            @if ($file['exists'])
                            <span>{{ $file['size_formatted'] }}</span>
                            <span>{{ $file['modified'] ? \Carbon\Carbon::parse($file['modified'])->diffForHumans() : 'N/A' }}</span>
                            @else
                            <span class="text-[#6B7280]">Not created yet</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function pollingTable() {
            return {
                polls: [],
                expanded: null,
                refreshTimer: null,

                init() {
                    this.syncFromManager();
                    this.refreshTimer = setInterval(() => this.refreshStatus(), 3000);
                },

                destroy() {
                    if (this.refreshTimer) clearInterval(this.refreshTimer);
                },

                getSaved(name) {
                    if (!window.PollingManager) return null;
                    const all = PollingManager.getSavedConfigs();
                    return all[name] || null;
                },

                getDefaultInterval(key) {
                    const serverPolls = @json($clientPolls);
                    const sp = serverPolls.find(p => p.key === key);
                    return sp ? sp.default_interval : 30;
                },

                syncFromManager() {
                    const serverPolls = @json($clientPolls);
                    const serverTracker = @json($pollTracker);

                    this.polls = serverPolls.map(sp => {
                        const saved = this.getSaved(sp.key);
                        const tracker = serverTracker[sp.key] || {};
                        const intervalSec = saved?.intervalMs ? Math.round(saved.intervalMs / 1000) : sp.default_interval;
                        return {
                            key: sp.key,
                            name: sp.name,
                            description: sp.description,
                            page: sp.page,
                            defaultInterval: sp.default_interval,
                            intervalMs: saved?.intervalMs || (sp.default_interval * 1000),
                            intervalSec: saved?.intervalMs ? Math.round(saved.intervalMs / 1000) : sp.default_interval,
                            isActive: saved?.isActive !== undefined ? saved.isActive : tracker.is_active !== undefined ? tracker.is_active : true,
                            lastRun: tracker.last_run_at ? new Date(tracker.last_run_at.replace(' ', 'T')).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' }) : null,
                            runCount: tracker.run_count || 0,
                        };
                    });
                },

                refreshStatus() {
                    if (!window.PollingManager) return;
                    const saved = PollingManager.getSavedConfigs();
                    for (const poll of this.polls) {
                        const cfg = saved[poll.key];
                        if (cfg) {
                            poll.isActive = cfg.isActive;
                            if (cfg.intervalMs) {
                                poll.intervalMs = cfg.intervalMs;
                                poll.intervalSec = Math.round(cfg.intervalMs / 1000);
                            }
                        }
                    }
                },

                toggleExpand(key) {
                    this.expanded = this.expanded === key ? null : key;
                },

                togglePoll(poll) {
                    poll.isActive = !poll.isActive;
                    if (window.PollingManager) {
                        if (poll.isActive) {
                            PollingManager.resumePoll(poll.key, poll.intervalMs);
                        } else {
                            PollingManager.pausePoll(poll.key, poll.intervalMs);
                        }
                    }
                },

                updateInterval(poll) {
                    poll.intervalSec = Math.max(5, Math.min(300, poll.intervalSec));
                    poll.intervalMs = poll.intervalSec * 1000;
                    if (window.PollingManager) {
                        PollingManager.setInterval(poll.key, poll.intervalMs);
                    }
                },

                pollAllNow() {
                    if (window.PollingManager) {
                        PollingManager.pollAll();
                    }
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    for (const poll of this.polls) {
                        fetch('/controlPanel/tracker/sync', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify({
                                key: poll.key,
                                type: 'poll',
                                is_active: poll.isActive,
                                interval_ms: poll.intervalMs,
                                last_run_at: 1,
                            }),
                        }).catch(() => {});
                        poll.lastRun = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                    }
                },

                resetAll() {
                    if (window.PollingManager) {
                        PollingManager.resetAllAndClearSaved();
                    }
                    this.syncFromManager();
                },
            }
        }
    </script>
    @endpush
</x-layouts.app>
