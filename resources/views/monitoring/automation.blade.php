<x-layouts.app title="Automation">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Automation</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">All automated processes — scheduled tasks, polls, observers, and system health.</p>
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
                    <span class="text-xs text-[#94A3B8]">Client Polling</span>
                    <i class="fas fa-sync text-[#22C55E] text-sm"></i>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ count($clientPolls) }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Real-time browser polls</p>
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

        {{-- Row 2: Scheduled Tasks + Client Polling --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Scheduler --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-clock text-[#3B82F6] mr-2"></i>Scheduled Tasks (Cron)</h2>
                    <span class="text-xs text-[#94A3B8]">routes/console.php</span>
                </div>
                <div class="space-y-1.5">
                    @forelse ($schedulerTasks as $task)
                    <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-[#E6EDF3] truncate">{{ $task['name'] }}</p>
                            <p class="text-[10px] text-[#94A3B8] truncate">{{ $task['command'] }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-0.5 text-[10px] font-medium
                            {{ $task['type'] === 'frequent' ? 'bg-[#22C55E]/20 text-[#22C55E]' : '' }}
                            {{ $task['type'] === 'hourly' ? 'bg-[#3B82F6]/20 text-[#3B82F6]' : '' }}
                            {{ $task['type'] === 'slow' ? 'bg-[#F59E0B]/20 text-[#F59E0B]' : '' }}
                        ">{{ $task['frequency'] }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-[#94A3B8] text-center py-4">No scheduled tasks found.</p>
                    @endforelse
                </div>
            </div>

            {{-- Client Polling --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]"><i class="fas fa-sync text-[#22C55E] mr-2"></i>Client-Side Polling</h2>
                    <span class="text-xs text-[#94A3B8]">Browser setInterval</span>
                </div>
                <div class="space-y-1.5">
                    @foreach ($clientPolls as $poll)
                    <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ $poll['name'] }}</p>
                            <p class="text-[10px] text-[#94A3B8]">{{ $poll['location'] }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-[#14B8A6]/20 px-2.5 py-0.5 text-[10px] font-medium text-[#14B8A6]">{{ $poll['interval'] }}</span>
                    </div>
                    @endforeach
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
                    <span class="text-xs text-[#94A3B8]">Last 10 runs</span>
                </div>
                <div class="space-y-2">
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
</x-layouts.app>
