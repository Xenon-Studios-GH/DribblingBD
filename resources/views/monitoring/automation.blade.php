<x-layouts.app title="Automation">
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Automation</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Monitor all automated processes — audits, scheduler, and system health.</p>
        </div>

        {{-- Row 1: Status Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Audit Status</span>
                    <span class="flex h-2 w-2 rounded-full {{ $auditLogs->isNotEmpty() && $auditLogs->first()['issues'] === 0 ? 'bg-[#22C55E]' : 'bg-[#F59E0B]' }}"></span>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ $auditLogs->isNotEmpty() ? $auditLogs->first()['ran_at']->diffForHumans() : 'Never' }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Last audit run</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Pending Confirmations</span>
                    <span class="flex h-2 w-2 rounded-full {{ $pendingTransactions > 0 ? 'bg-[#F59E0B]' : 'bg-[#22C55E]' }}"></span>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ $pendingTransactions }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">৳{{ number_format($pendingAmount, 0) }} total</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Scheduled Tasks</span>
                    <span class="flex h-2 w-2 rounded-full bg-[#3B82F6]"></span>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ count($schedulerTasks) }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">Registered commands</p>
            </div>

            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-[#94A3B8]">Last Cleanup</span>
                    <span class="flex h-2 w-2 rounded-full {{ $lastCleanup ? 'bg-[#22C55E]' : 'bg-[#6B7280]' }}"></span>
                </div>
                <p class="text-lg font-bold text-[#E6EDF3]">{{ $lastCleanup ? $lastCleanup->diffForHumans() : 'Never' }}</p>
                <p class="text-xs text-[#94A3B8] mt-1">System cleanup</p>
            </div>
        </div>

        {{-- Row 2: Audit History + Scheduler --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Audit History --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Audit History</h2>
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
                    <p class="text-xs text-[#94A3B8] text-center py-4">No audit runs yet. The audit runs every 5 minutes.</p>
                    @endforelse
                </div>
            </div>

            {{-- Scheduled Tasks --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Scheduled Tasks</h2>
                    <span class="text-xs text-[#94A3B8]">routes/console.php</span>
                </div>
                <div class="space-y-1.5">
                    @forelse ($schedulerTasks as $task)
                    <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2.5">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-[#E6EDF3] truncate">{{ $task['name'] }}</p>
                            <p class="text-[10px] text-[#94A3B8] truncate">{{ $task['command'] }}</p>
                        </div>
                        <span class="shrink-0 rounded-full bg-[#2563EB]/20 px-2.5 py-0.5 text-[10px] font-medium text-[#3B82F6]">{{ $task['frequency'] }}</span>
                    </div>
                    @empty
                    <p class="text-xs text-[#94A3B8] text-center py-4">No scheduled tasks found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Row 3: Orders Breakdown + Log Files --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- Orders by Status --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Orders by Status</h2>
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

            {{-- Log Files --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">System Logs</h2>
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

                <div class="mt-4 pt-3 border-t border-[#232A36]">
                    <h3 class="text-xs font-medium text-[#94A3B8] mb-2">Quick Actions</h3>
                    <div class="flex flex-wrap gap-2">
                        <form action="{{ admin_route('monitoring.run-audit') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="rounded-lg border border-[#232A36] px-3 py-1.5 text-xs text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                                <i class="fas fa-play mr-1"></i> Run Audit Now
                            </button>
                        </form>
                        <a href="{{ admin_route('monitoring.index') }}" class="rounded-lg border border-[#232A36] px-3 py-1.5 text-xs text-[#E6EDF3] hover:bg-[#1C2333] transition-colors">
                            <i class="fas fa-history mr-1"></i> View Activity Logs
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
