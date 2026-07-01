@php use App\Models\FinanceCategory; @endphp
<x-layouts.app title="System Controller">
    <div class="space-y-6" x-data="{ tab: {{ request('tab', 1) }} }">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">System Controller</h1>
        </div>

        <!-- Tabs -->
        <div class="flex gap-1 border-b border-[#232A36]">
            <button @click="tab = 1" :class="tab === 1 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-3 text-sm font-medium transition-colors">
                Finance Categories
            </button>
            <button @click="tab = 2" :class="tab === 2 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-3 text-sm font-medium transition-colors">
                Fixed Amounts
            </button>
            <button @click="tab = 3" :class="tab === 3 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-3 text-sm font-medium transition-colors">
                Automation
            </button>
            <button @click="tab = 4" :class="tab === 4 ? 'border-b-2 border-[#3B82F6] text-[#E6EDF3]' : 'text-[#94A3B8] hover:text-[#E6EDF3]'" class="px-4 py-3 text-sm font-medium transition-colors">
                Monitor
            </button>
        </div>

        <!-- Tab 1: Finance Categories -->
        <div x-show="tab === 1" class="space-y-6">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Category Mappings</h2>
                </div>
                <form method="POST" action="{{ admin_route('system-controller.mappings') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @csrf
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Advanced Payment →</label>
                        <select name="finance_category_advanced_payment" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">Select category</option>
                            @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryMappings['advanced_payment'] === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Product Sales →</label>
                        <select name="finance_category_product_sales" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">Select category</option>
                            @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryMappings['product_sales'] === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">DTF Sales →</label>
                        <select name="finance_category_dtf_sales" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">Select category</option>
                            @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryMappings['dtf_sales'] === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Patch Sales →</label>
                        <select name="finance_category_patch_sales" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                            <option value="">Select category</option>
                            @foreach($incomeCategories as $cat)
                                <option value="{{ $cat->id }}" @selected((string) $categoryMappings['patch_sales'] === (string) $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                            Save Mappings
                        </button>
                    </div>
                </form>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Income Categories -->
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-[#E6EDF3]">Income Categories</h2>
                        <button @click="$refs.incomeForm.classList.toggle('hidden')" class="text-xs text-[#3B82F6] hover:underline">+ Add</button>
                    </div>
                    <form method="POST" action="{{ admin_route('system-controller.categories.store') }}" x-ref="incomeForm" class="hidden mb-4 p-3 rounded-lg bg-[#1C2333] space-y-2">
                        @csrf
                        <input type="hidden" name="type" value="income">
                        <input type="text" name="name" placeholder="Category name" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        <input type="text" name="description" placeholder="Description (optional)" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        <button type="submit" class="rounded-lg bg-[#22C55E] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#16A34A]">Create</button>
                    </form>
                    <div class="space-y-2">
                        @forelse($incomeCategories as $cat)
                        <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2">
                            <div>
                                <span class="text-sm text-[#E6EDF3] font-medium">{{ $cat->name }}</span>
                                <span class="text-xs text-[#94A3B8] ml-2">({{ $cat->transactions_count }} txns)</span>
                                @if(!$cat->is_active)<span class="text-xs text-[#EF4444] ml-1">inactive</span>@endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="$refs.catEdit{{ $cat->id }}.classList.toggle('hidden')" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                                <form method="POST" action="{{ admin_route('system-controller.categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-[#EF4444] hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                        <form method="POST" action="{{ admin_route('system-controller.categories.update', $cat) }}" x-ref="catEdit{{ $cat->id }}" class="hidden mb-2 p-3 rounded-lg bg-[#161B22] space-y-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $cat->name }}" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                            <input type="text" name="description" value="{{ $cat->description }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                            <label class="flex items-center gap-2 text-sm text-[#94A3B8]">
                                <input type="checkbox" name="is_active" value="1" @checked($cat->is_active) class="rounded border-[#232A36] bg-[#0F1117]">
                                Active
                            </label>
                            <button type="submit" class="rounded-lg bg-[#3B82F6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#2563EB]">Update</button>
                        </form>
                        @empty
                        <p class="text-sm text-[#94A3B8]">No income categories.</p>
                        @endforelse
                    </div>
                </x-card>

                <!-- Expense Categories -->
                <x-card>
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-[#E6EDF3]">Expense Categories</h2>
                        <button @click="$refs.expenseForm.classList.toggle('hidden')" class="text-xs text-[#3B82F6] hover:underline">+ Add</button>
                    </div>
                    <form method="POST" action="{{ admin_route('system-controller.categories.store') }}" x-ref="expenseForm" class="hidden mb-4 p-3 rounded-lg bg-[#1C2333] space-y-2">
                        @csrf
                        <input type="hidden" name="type" value="expense">
                        <input type="text" name="name" placeholder="Category name" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        <input type="text" name="description" placeholder="Description (optional)" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                        <button type="submit" class="rounded-lg bg-[#EF4444] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#DC2626]">Create</button>
                    </form>
                    <div class="space-y-2">
                        @forelse($expenseCategories as $cat)
                        <div class="flex items-center justify-between rounded-lg bg-[#1C2333] px-3 py-2">
                            <div>
                                <span class="text-sm text-[#E6EDF3] font-medium">{{ $cat->name }}</span>
                                <span class="text-xs text-[#94A3B8] ml-2">({{ $cat->transactions_count }} txns)</span>
                                @if(!$cat->is_active)<span class="text-xs text-[#EF4444] ml-1">inactive</span>@endif
                            </div>
                            <div class="flex items-center gap-2">
                                <button @click="$refs.expCatEdit{{ $cat->id }}.classList.toggle('hidden')" class="text-xs text-[#3B82F6] hover:underline">Edit</button>
                                <form method="POST" action="{{ admin_route('system-controller.categories.destroy', $cat) }}" class="inline" onsubmit="return confirm('Delete this category?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-[#EF4444] hover:underline">Delete</button>
                                </form>
                            </div>
                        </div>
                        <form method="POST" action="{{ admin_route('system-controller.categories.update', $cat) }}" x-ref="expCatEdit{{ $cat->id }}" class="hidden mb-2 p-3 rounded-lg bg-[#161B22] space-y-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $cat->name }}" required class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                            <input type="text" name="description" value="{{ $cat->description }}" class="w-full rounded-lg border border-[#232A36] bg-[#0F1117] px-3 py-2 text-sm text-[#E6EDF3]">
                            <label class="flex items-center gap-2 text-sm text-[#94A3B8]">
                                <input type="checkbox" name="is_active" value="1" @checked($cat->is_active) class="rounded border-[#232A36] bg-[#0F1117]">
                                Active
                            </label>
                            <button type="submit" class="rounded-lg bg-[#3B82F6] px-3 py-1.5 text-xs font-medium text-white hover:bg-[#2563EB]">Update</button>
                        </form>
                        @empty
                        <p class="text-sm text-[#94A3B8]">No expense categories.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>

        <!-- Tab 2: Fixed Amounts -->
        <div x-show="tab === 2">
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Fixed Amounts &amp; Rates</h2>
                </div>
                <form method="POST" action="{{ admin_route('system-controller.fixed-amounts') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @csrf
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">DTF Fee (৳)</label>
                        <input type="number" name="dtf_fee" value="{{ $fixedAmounts['dtf_fee'] }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Patch Quantity</label>
                        <input type="number" name="patch_quantity" value="{{ $fixedAmounts['patch_quantity'] }}" disabled class="w-full rounded-xl border border-[#232A36] bg-[#1C2333] px-3 py-2 text-sm text-[#64748B] cursor-not-allowed">
                        <p class="text-[10px] text-[#64748B] mt-0.5">Managed in config/shop.php</p>
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Shipping: Dhaka (৳)</label>
                        <input type="number" name="shipping_dhaka_rate" value="{{ $fixedAmounts['dhaka_rate'] }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Shipping: Outside Dhaka (৳)</label>
                        <input type="number" name="shipping_outside_rate" value="{{ $fixedAmounts['outside_rate'] }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Free Shipping Threshold (৳)</label>
                        <input type="number" name="shipping_free_threshold" value="{{ $fixedAmounts['free_threshold'] }}" class="w-full rounded-xl border border-[#232A36] bg-[#161B22] px-3 py-2 text-sm text-[#E6EDF3]">
                    </div>
                    <div>
                        <label class="block text-sm text-[#94A3B8] mb-1">Patch Product Name Query</label>
                        <input type="text" name="patch_name_query" value="{{ $fixedAmounts['patch_name_query'] }}" disabled class="w-full rounded-xl border border-[#232A36] bg-[#1C2333] px-3 py-2 text-sm text-[#64748B] cursor-not-allowed">
                        <p class="text-[10px] text-[#64748B] mt-0.5">Managed in config/shop.php</p>
                    </div>
                    <div class="md:col-span-2 lg:col-span-3">
                        <button type="submit" class="rounded-xl bg-[#3B82F6] px-4 py-2 text-sm font-medium text-white hover:bg-[#2563EB] transition-colors">
                            Save Fixed Amounts
                        </button>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Tab 3: Automation -->
        <div x-show="tab === 3" class="space-y-6">
            <!-- Polls -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Live Page Refreshes (Polls)</h2>
                    <div class="flex items-center gap-2">
                        <button onclick="runAllPolls()" class="rounded-lg bg-[#22C55E]/10 px-3 py-1.5 text-xs font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                            <i class="fas fa-play mr-1"></i> Run All Now
                        </button>
                        <button onclick="resetAllPolls()" class="rounded-lg bg-[#F59E0B]/10 px-3 py-1.5 text-xs font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20 transition-colors">
                            <i class="fas fa-undo mr-1"></i> Reset All
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                                <th class="pb-3 font-medium">Poll</th>
                                <th class="pb-3 font-medium">Page</th>
                                <th class="pb-3 font-medium">Description</th>
                                <th class="pb-3 font-medium text-right">Interval</th>
                                <th class="pb-3 font-medium text-center">Active</th>
                                <th class="pb-3 font-medium text-right">Last Run</th>
                                <th class="pb-3 font-medium text-right">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clientPolls as $poll)
                            @php $tracked = $pollTracker[$poll['key']] ?? null; @endphp
                            <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                                <td class="py-3 text-[#E6EDF3] font-medium">{{ $poll['name'] }}</td>
                                <td class="py-3 text-[#94A3B8]">{{ $poll['page'] }}</td>
                                <td class="py-3 text-[#94A3B8] max-w-[200px] truncate">{{ $poll['description'] }}</td>
                                <td class="py-3 text-right text-[#E6EDF3]">{{ $tracked?->interval_ms ? ($tracked->interval_ms / 1000) . 's' : ($poll['default_interval'] / 1000) . 's' }}</td>
                                <td class="py-3 text-center">
                                    @if($tracked)
                                        @if($tracked->is_active)
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2 py-0.5 text-xs font-medium text-[#22C55E]">On</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-[#EF4444]/10 px-2 py-0.5 text-xs font-medium text-[#EF4444]">Off</span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-[#22C55E]/10 px-2 py-0.5 text-xs font-medium text-[#22C55E]">On</span>
                                    @endif
                                </td>
                                <td class="py-3 text-right text-[#94A3B8] text-xs">
                                    {{ $tracked?->last_run_at ? \Carbon\Carbon::parse($tracked->last_run_at)->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-3 text-right text-[#94A3B8]">{{ $tracked?->run_count ?? 0 }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Scheduled Tasks -->
            <x-card>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Scheduled Tasks</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[#232A36] text-left text-[#94A3B8]">
                                <th class="pb-3 font-medium">Task</th>
                                <th class="pb-3 font-medium">Frequency</th>
                                <th class="pb-3 font-medium">Description</th>
                                <th class="pb-3 font-medium text-right">Last Run</th>
                                <th class="pb-3 font-medium text-right">Count</th>
                                <th class="pb-3 font-medium text-center">Run Now</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scheduledTasks as $task)
                            @php $tracked = $taskTracker[$task['command']] ?? null; @endphp
                            <tr class="border-b border-[#232A36]/50 hover:bg-[#1C2333]">
                                <td class="py-3 text-[#E6EDF3] font-medium">{{ $task['name'] }}</td>
                                <td class="py-3 text-[#94A3B8]">{{ $task['frequency'] }}</td>
                                <td class="py-3 text-[#94A3B8] max-w-[250px] truncate">{{ $task['description'] }}</td>
                                <td class="py-3 text-right text-[#94A3B8] text-xs">
                                    {{ $tracked?->last_run_at ? \Carbon\Carbon::parse($tracked->last_run_at)->diffForHumans() : 'Never' }}
                                </td>
                                <td class="py-3 text-right text-[#94A3B8]">{{ $tracked?->run_count ?? 0 }}</td>
                                <td class="py-3 text-center">
                                    <form method="POST" action="{{ route('monitoring.run-task') }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="command" value="{{ $task['command'] }}">
                                        <input type="hidden" name="task_name" value="{{ $task['name'] }}">
                                        <button type="submit" onclick="return confirm('Run {{ $task['name'] }} now?')" class="rounded-lg bg-[#3B82F6]/10 px-3 py-1 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                                            <i class="fas fa-play mr-1"></i> Run
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Tab 4: Monitor -->
        <div x-show="tab === 4" class="space-y-6">
            <!-- Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <x-card class="text-center">
                    <p class="text-2xl font-bold text-[#E6EDF3]">{{ $pendingPackedCount }}</p>
                    <p class="text-xs text-[#F59E0B] mt-1">Pending Packing</p>
                </x-card>
                <x-card class="text-center">
                    <p class="text-2xl font-bold text-[#E6EDF3]">{{ $pendingPaymentCount }}</p>
                    <p class="text-xs text-[#22C55E] mt-1">Pending Payments</p>
                </x-card>
                <x-card class="text-center">
                    <p class="text-2xl font-bold text-[#E6EDF3]">৳{{ number_format($pendingPaymentTotal, 0) }}</p>
                    <p class="text-xs text-[#94A3B8] mt-1">Pending Amount</p>
                </x-card>
                <x-card class="text-center">
                    <p class="text-2xl font-bold text-[#E6EDF3]">{{ $autoRestoredCount }}</p>
                    <p class="text-xs text-[#3B82F6] mt-1">Auto-Restored Orders</p>
                </x-card>
            </div>

            <!-- Orders by Status -->
            <x-card>
                <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Orders by Status</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach(['on_hold', 'packed', 'picked', 'delivered', 'out_of_stock', 'return', 'refund', 'pending'] as $status)
                    @php $s = $ordersByStatus[$status] ?? null; @endphp
                    <div class="rounded-lg bg-[#1C2333] px-3 py-2 text-center">
                        <p class="text-lg font-bold text-[#E6EDF3]">{{ $s ? $s->count : 0 }}</p>
                        <p class="text-[10px] text-[#94A3B8] uppercase tracking-wider">{{ str_replace('_', ' ', $status) }}</p>
                        @if($s && $s->total > 0)
                        <p class="text-[10px] text-[#64748B]">৳{{ number_format($s->total, 0) }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-card>

            <!-- Quick Links -->
            <x-card>
                <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Quick Links</h2>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ admin_route('orders.packed-pending') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#F59E0B]/10 px-4 py-2 text-sm font-medium text-[#F59E0B] hover:bg-[#F59E0B]/20 transition-colors">
                        <i class="fas fa-boxes"></i> Packed Pending ({{ $pendingPackedCount }})
                    </a>
                    <a href="{{ admin_route('finance.pending-orders') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#22C55E]/10 px-4 py-2 text-sm font-medium text-[#22C55E] hover:bg-[#22C55E]/20 transition-colors">
                        <i class="fas fa-credit-card"></i> Pending Payments ({{ $pendingPaymentCount }})
                    </a>
                    <a href="{{ admin_route('monitoring.automation') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#3B82F6]/10 px-4 py-2 text-sm font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">
                        <i class="fas fa-cog"></i> Automation Dashboard
                    </a>
                    <a href="{{ admin_route('stock.management') }}" class="inline-flex items-center gap-2 rounded-xl bg-[#A855F7]/10 px-4 py-2 text-sm font-medium text-[#A855F7] hover:bg-[#A855F7]/20 transition-colors">
                        <i class="fas fa-warehouse"></i> Stock Management
                    </a>
                </div>
            </x-card>

            <!-- Recent Activity -->
            <x-card>
                <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Recent Activity</h2>
                <div class="space-y-3">
                    @forelse($recentWorkLogs as $log)
                    <div class="flex items-start gap-3 border-b border-[#232A36]/50 pb-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#1C2333]">
                            <i class="fas fa-circle text-[8px] text-[#3B82F6]"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-[#E6EDF3] font-medium">{{ $log->action }}</p>
                            <p class="text-xs text-[#94A3B8] truncate">{{ $log->description ?? '' }}</p>
                        </div>
                        <span class="text-xs text-[#64748B] whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-[#94A3B8]">No recent activity.</p>
                    @endforelse
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function runAllPolls() {
            Swal.fire({
                title: 'Run All Polls Now?',
                text: 'This will trigger all page refresh polls immediately.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#22C55E',
                confirmButtonText: 'Run All',
                cancelButtonText: 'Cancel',
                background: '#161B22',
                color: '#E6EDF3',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("tracker.sync") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ key: 'poll-run-all', is_active: true, type: 'poll' }),
                    }).then(() => {
                        Swal.fire({ icon: 'success', title: 'Polls Triggered', text: 'All polls have been signaled to run.', background: '#161B22', color: '#E6EDF3', confirmButtonColor: '#3B82F6' });
                    });
                }
            });
        }

        function resetAllPolls() {
            Swal.fire({
                title: 'Reset All Polls?',
                text: 'This will reset all poll intervals to their default values.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#F59E0B',
                confirmButtonText: 'Reset All',
                cancelButtonText: 'Cancel',
                background: '#161B22',
                color: '#E6EDF3',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route("tracker.sync") }}', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ key: 'poll-reset-all', is_active: true, type: 'poll' }),
                    }).then(() => {
                        Swal.fire({ icon: 'success', title: 'Polls Reset', text: 'All poll intervals have been reset to defaults.', background: '#161B22', color: '#E6EDF3', confirmButtonColor: '#3B82F6' });
                    });
                }
            });
        }
    </script>
    @endpush
</x-layouts.app>
