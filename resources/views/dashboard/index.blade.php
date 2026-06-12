<x-layouts.app title="Dashboard">
    <div class="space-y-8">
        <div>
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Dashboard</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Welcome back, {{ Auth::user()->name }} · <span class="text-[#3B82F6]">{{ ucfirst(Auth::user()->role) }}</span></p>
        </div>

        {{-- Key Stats Row --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ admin_route('stock.management') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#3B82F6] transition-colors group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Total Stock</p>
                        <p class="mt-1 text-xl font-bold text-[#E6EDF3] group-hover:text-[#3B82F6] transition-colors">{{ number_format($totalStock) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                        <i class="fas fa-cubes text-[#3B82F6]"></i>
                    </div>
                </div>
            </a>
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <a href="{{ admin_route('stock.in') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#22C55E] transition-colors group">
            @else
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
            @endif
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Stock In Today</p>
                        <p class="mt-1 text-xl font-bold text-[#22C55E]">+{{ number_format($stockInToday) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#22C55E]/10">
                        <i class="fas fa-plus-circle text-[#22C55E]"></i>
                    </div>
                </div>
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            </a>
            @else
            </div>
            @endif
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <a href="{{ admin_route('stock.out') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#EF4444] transition-colors group">
            @else
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
            @endif
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Stock Out Today</p>
                        <p class="mt-1 text-xl font-bold text-[#EF4444]">{{ $stockOutToday > 0 ? '-' . number_format($stockOutToday) : '0' }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#EF4444]/10">
                        <i class="fas fa-minus-circle text-[#EF4444]"></i>
                    </div>
                </div>
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            </a>
            @else
            </div>
            @endif
            <a href="{{ admin_route('orders.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#F59E0B] transition-colors group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Total Orders</p>
                        <p class="mt-1 text-xl font-bold text-[#E6EDF3] group-hover:text-[#F59E0B] transition-colors">{{ $totalOrders }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#F59E0B]/10">
                        <i class="fas fa-shopping-bag text-[#F59E0B]"></i>
                    </div>
                </div>
            </a>
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <a href="{{ admin_route('finance.dashboard') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#22C55E] transition-colors group">
            @else
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
            @endif
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Balance</p>
                        <p class="mt-1 text-xl font-bold {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">৳{{ number_format($balance, 0) }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#22C55E]/10">
                        <i class="fas fa-wallet text-[#22C55E]"></i>
                    </div>
                </div>
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            </a>
            @else
            </div>
            @endif
            @if (Auth::user()->role === 'superadmin')
            <a href="{{ admin_route('workers.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#A855F7] transition-colors group">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-[#94A3B8]">Workers</p>
                        <p class="mt-1 text-xl font-bold text-[#E6EDF3] group-hover:text-[#A855F7] transition-colors">{{ $totalWorkers }}</p>
                    </div>
                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#A855F7]/10">
                        <i class="fas fa-users text-[#A855F7]"></i>
                    </div>
                </div>
            </a>
            @endif
        </div>

        {{-- Orders Section --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-[#E6EDF3]">Orders</h2>
                <a href="{{ admin_route('orders.index') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <a href="{{ admin_route('orders.index', ['status' => 'pending']) }}" class="rounded-xl border border-[#F59E0B]/30 bg-[#F59E0B]/5 p-4 hover:bg-[#F59E0B]/10 transition-colors">
                    <p class="text-xs text-[#F59E0B]">Pending</p>
                    <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $orderCounts['pending'] }}</p>
                </a>
                <a href="{{ admin_route('orders.index', ['status' => 'on_hold']) }}" class="rounded-xl border border-[#3B82F6]/30 bg-[#3B82F6]/5 p-4 hover:bg-[#3B82F6]/10 transition-colors">
                    <p class="text-xs text-[#3B82F6]">On Hold</p>
                    <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $orderCounts['on_hold'] }}</p>
                </a>
                <a href="{{ admin_route('orders.index', ['status' => 'processing']) }}" class="rounded-xl border border-[#A855F7]/30 bg-[#A855F7]/5 p-4 hover:bg-[#A855F7]/10 transition-colors">
                    <p class="text-xs text-[#A855F7]">Processing</p>
                    <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $orderCounts['processing'] }}</p>
                </a>
                <a href="{{ admin_route('orders.index', ['status' => 'delivered']) }}" class="rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/5 p-4 hover:bg-[#22C55E]/10 transition-colors">
                    <p class="text-xs text-[#22C55E]">Delivered</p>
                    <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $orderCounts['delivered'] }}</p>
                </a>
                <a href="{{ admin_route('orders.index', ['status' => 'return']) }}" class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/5 p-4 hover:bg-[#EF4444]/10 transition-colors">
                    <p class="text-xs text-[#EF4444]">Return</p>
                    <p class="mt-1 text-2xl font-bold text-[#E6EDF3]">{{ $orderCounts['return'] }}</p>
                </a>
                <a href="{{ admin_route('orders.create') }}" class="rounded-xl border border-dashed border-[#232A36] bg-transparent p-4 hover:border-[#22C55E] hover:bg-[#22C55E]/5 transition-colors flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-plus text-[#94A3B8] mb-1"></i>
                        <p class="text-xs text-[#94A3B8]">New Order</p>
                    </div>
                </a>
            </div>

            @if ($recentOrders->count())
            <div class="mt-4 rounded-xl border border-[#232A36] bg-[#161B22]">
                <div class="divide-y divide-[#232A36]">
                    @foreach ($recentOrders as $o)
                    <a href="{{ admin_route('orders.show', $o->order_no) }}" class="flex items-center justify-between px-4 py-3 hover:bg-[#1C2333] transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="text-sm font-medium text-[#3B82F6]">{{ $o->order_no }}</span>
                            <span class="text-xs text-[#94A3B8] truncate">{{ $o->customer_name }}</span>
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            <span class="text-xs text-[#6B7280]">৳{{ number_format($o->total_amount, 0) }}</span>
                            <span class="text-[10px] capitalize px-2 py-0.5 rounded-full {{ $o->status === 'pending' ? 'text-[#F59E0B] bg-[#F59E0B]/10' : ($o->status === 'delivered' ? 'text-[#22C55E] bg-[#22C55E]/10' : ($o->status === 'processing' ? 'text-[#A855F7] bg-[#A855F7]/10' : 'text-[#94A3B8] bg-[#232A36]')) }}">{{ str_replace('_', ' ', $o->status) }}</span>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Stock & Products Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Stock & Products</h2>
                    <a href="{{ admin_route('stock.management') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ admin_route('stock.management') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#3B82F6] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                                <i class="fas fa-box text-[#3B82F6]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Total Products</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">{{ $totalProducts }}</p>
                            </div>
                        </div>
                    </a>
                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ admin_route('products.create') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#22C55E] transition-colors">
                    @else
                    <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                    @endif
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#22C55E]/10">
                                <i class="fas fa-plus-circle text-[#22C55E]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Active Products</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">{{ $activeProducts }}</p>
                            </div>
                        </div>
                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    </a>
                    @else
                    </div>
                    @endif
                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ admin_route('stock.activity') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#F59E0B] transition-colors">
                    @else
                    <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                    @endif
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F59E0B]/10">
                                <i class="fas fa-clock text-[#F59E0B]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Stock Activity</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">View</p>
                            </div>
                        </div>
                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    </a>
                    @else
                    </div>
                    @endif
                    <a href="{{ admin_route('stock.management') }}" class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/5 p-4 hover:bg-[#EF4444]/10 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EF4444]/10">
                                <i class="fas fa-exclamation-triangle text-[#EF4444]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#EF4444]">Low Stock</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">{{ $lowStockProducts }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Finance Section --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Finance</h2>
                    <a href="{{ admin_route('finance.dashboard') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ admin_route('finance.transactions', ['type' => 'income']) }}" class="rounded-xl border border-[#22C55E]/30 bg-[#22C55E]/5 p-4 hover:bg-[#22C55E]/10 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#22C55E]/10">
                                <i class="fas fa-arrow-up text-[#22C55E]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Total Income</p>
                                <p class="text-lg font-bold text-[#22C55E]">৳{{ number_format($totalIncome, 0) }}</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ admin_route('finance.transactions', ['type' => 'expense']) }}" class="rounded-xl border border-[#EF4444]/30 bg-[#EF4444]/5 p-4 hover:bg-[#EF4444]/10 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EF4444]/10">
                                <i class="fas fa-arrow-down text-[#EF4444]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Total Expense</p>
                                <p class="text-lg font-bold text-[#EF4444]">৳{{ number_format($totalExpense, 0) }}</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ admin_route('finance.reports') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#3B82F6] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                                <i class="fas fa-chart-bar text-[#3B82F6]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">This Month Income</p>
                                <p class="text-lg font-bold text-[#22C55E]">৳{{ number_format($monthIncome, 0) }}</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ admin_route('finance.reports') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#EF4444] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EF4444]/10">
                                <i class="fas fa-chart-line text-[#EF4444]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">This Month Expense</p>
                                <p class="text-lg font-bold text-[#EF4444]">৳{{ number_format($monthExpense, 0) }}</p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endif
        </div>

        {{-- Website & Activity Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Website --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Website</h2>
                    <a href="{{ admin_route('website.dashboard') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ admin_route('website.projects') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#3B82F6] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                                <i class="fas fa-project-diagram text-[#3B82F6]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Projects</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">{{ $totalWebsiteProjects }}</p>
                            </div>
                        </div>
                    </a>
                    <a href="{{ admin_route('website.categories') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#22C55E] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#22C55E]/10">
                                <i class="fas fa-folder text-[#22C55E]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Categories</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">Manage</p>
                            </div>
                        </div>
                    </a>
                    @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                    <a href="{{ admin_route('website.customization.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#F59E0B] transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#F59E0B]/10">
                                <i class="fas fa-cog text-[#F59E0B]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Customization</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">Settings</p>
                            </div>
                        </div>
                    </a>
                    @endif
                    <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#A855F7]/10">
                                <i class="fas fa-globe text-[#A855F7]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-[#94A3B8]">Active on Site</p>
                                <p class="text-lg font-bold text-[#E6EDF3]">{{ $activeWebsiteProjects }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-[#E6EDF3]">Recent Activity</h2>
                    <a href="{{ admin_route('work-logs.index') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
                </div>
                <div class="rounded-xl border border-[#232A36] bg-[#161B22]">
                    @forelse ($recentLogs as $log)
                    <div class="flex items-start gap-3 px-4 py-3 border-b border-[#232A36] last:border-0">
                        <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                            <i class="fas fa-clock text-xs text-[#3B82F6]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-[#E6EDF3]">{{ $log->action }}</p>
                            <p class="text-xs text-[#94A3B8] truncate">{{ $log->description }} · {{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-4 py-8 text-center text-sm text-[#94A3B8]">No recent activity.</div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        {{-- Admin Quick Links (superadmin only) --}}
        @if (Auth::user()->role === 'superadmin')
        <div>
            <h2 class="text-lg font-semibold text-[#E6EDF3] mb-4">Administration</h2>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ admin_route('workers.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#A855F7] transition-colors text-center">
                    <i class="fas fa-users text-[#A855F7] text-xl mb-2"></i>
                    <p class="text-sm text-[#E6EDF3]">Users</p>
                </a>
                <a href="{{ admin_route('login-logs.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#3B82F6] transition-colors text-center">
                    <i class="fas fa-sign-in-alt text-[#3B82F6] text-xl mb-2"></i>
                    <p class="text-sm text-[#E6EDF3]">Login Logs</p>
                </a>
                <a href="{{ admin_route('work-logs.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#F59E0B] transition-colors text-center">
                    <i class="fas fa-clipboard-list text-[#F59E0B] text-xl mb-2"></i>
                    <p class="text-sm text-[#E6EDF3]">Work Logs</p>
                </a>
                <a href="{{ admin_route('inquiries.index') }}" class="rounded-xl border border-[#232A36] bg-[#161B22] p-4 hover:border-[#EF4444] transition-colors text-center relative">
                    <i class="fas fa-envelope text-[#EF4444] text-xl mb-2"></i>
                    <p class="text-sm text-[#E6EDF3]">Inquiries</p>
                    @if ($unreadInquiries > 0)
                    <span class="absolute top-2 right-2 flex h-5 w-5 items-center justify-center rounded-full bg-[#EF4444] text-[10px] font-bold text-white">{{ $unreadInquiries }}</span>
                    @endif
                </a>
            </div>
        </div>
        @endif
    </div>
</x-layouts.app>
