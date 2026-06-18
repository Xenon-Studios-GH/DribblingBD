<x-layouts.app title="Dashboard">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Control Panel</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">Welcome back, {{ Auth::user()->name }} · <span class="text-[#3B82F6]">{{ ucfirst(Auth::user()->role) }}</span></p>
            </div>
            <div class="flex items-center gap-2 text-xs text-[#94A3B8]">
                <i class="fas fa-calendar-alt"></i>
                <span id="dashboard-datetime">{{ now()->format('l, d F Y - h:i A') }}</span>
            </div>
        </div>

        {{-- Row 1: Core KPIs --}}
        @include('dashboard.partials._kpi-cards')

        {{-- Row 2: Orders + Stock Activity + Finance --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Orders Breakdown --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Orders</h2>
                    <a href="{{ admin_route('orders.index') }}" class="text-xs text-[#3B82F6] hover:underline">View All</a>
                </div>
                <div class="space-y-2">
                    <x-status-row label="Pending" :count="$orderCounts['pending']" color="#F59E0B" href="{{ admin_route('orders.index', ['status' => 'pending']) }}" />
                    <x-status-row label="On Hold" :count="$orderCounts['on_hold']" color="#3B82F6" href="{{ admin_route('orders.index', ['status' => 'on_hold']) }}" />
                    <x-status-row label="Packed" :count="$orderCounts['packed']" color="#EC4899" href="{{ admin_route('orders.index', ['status' => 'packed']) }}" />
                    <x-status-row label="Delivered" :count="$orderCounts['delivered']" color="#22C55E" href="{{ admin_route('orders.index', ['status' => 'delivered']) }}" />
                    <x-status-row label="Refund" :count="$orderCounts['refund']" color="#A855F7" href="{{ admin_route('orders.index', ['status' => 'refund']) }}" />
                    <x-status-row label="Return" :count="$orderCounts['return']" color="#EF4444" href="{{ admin_route('orders.index', ['status' => 'return']) }}" />
                </div>
                <div class="mt-3 pt-3 border-t border-[#232A36] flex items-center justify-between text-xs text-[#94A3B8]">
                    <span>This month: <strong class="text-[#E6EDF3]">{{ $ordersThisMonth }}</strong></span>
                    <span>Today: <strong class="text-[#E6EDF3]">{{ $ordersToday }}</strong></span>
                    <span>Revenue: <strong class="text-[#22C55E]">৳{{ number_format($revenueToday, 0) }}</strong></span>
                </div>
                @if ($recentOrders->count())
                <div class="mt-3 pt-3 border-t border-[#232A36] space-y-1.5">
                    @foreach ($recentOrders as $o)
                    <a href="{{ admin_route('orders.show', $o->order_no) }}" class="flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-[#1C2333] transition-colors">
                        <span class="text-xs font-medium text-[#3B82F6]">{{ $o->order_no }}</span>
                        <span class="text-xs text-[#6B7280]">৳{{ number_format($o->total_amount, 0) }}</span>
                    </a>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Stock Activity --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Stock & Products</h2>
                    <a href="{{ admin_route('stock.management') }}" class="text-xs text-[#3B82F6] hover:underline">Manage</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-lg bg-[#1C2333] p-3 text-center">
                        <p class="text-2xl font-bold text-[#3B82F6]">{{ $totalProducts }}</p>
                        <p class="text-xs text-[#94A3B8]">Total Products</p>
                    </div>
                    <div class="rounded-lg bg-[#1C2333] p-3 text-center">
                        <p class="text-2xl font-bold text-[#22C55E]">{{ $activeProducts }}</p>
                        <p class="text-xs text-[#94A3B8]">Active</p>
                    </div>
                    <div class="rounded-lg bg-[#F59E0B]/10 p-3 text-center">
                        <p class="text-2xl font-bold text-[#F59E0B]">{{ number_format($totalStock) }}</p>
                        <p class="text-xs text-[#94A3B8]">All Stock Qty</p>
                    </div>
                    <div class="rounded-lg bg-[#22C55E]/10 p-3 text-center">
                        <p class="text-lg font-bold text-[#22C55E]">৳{{ number_format($stockValue, 0) }}</p>
                        <p class="text-xs text-[#94A3B8]">Stock Value</p>
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-[#94A3B8]">
                    <span>In today: <strong class="text-[#22C55E]">+{{ number_format($stockInToday) }}</strong></span>
                    <span>Out today: <strong class="text-[#EF4444]">-{{ number_format($stockOutToday) }}</strong></span>
                    <span>Out of stock: <strong class="text-[#EF4444]">{{ $outOfStockProducts }}</strong></span>
                </div>
            </div>

            {{-- Finance Snapshot --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Finance</h2>
                    <a href="{{ admin_route('finance.dashboard') }}" class="text-xs text-[#3B82F6] hover:underline">Details</a>
                </div>
                <div class="space-y-2.5">
                    <div class="flex items-center justify-between px-2">
                        <span class="text-xs text-[#94A3B8]">Balance</span>
                        <span class="text-sm font-bold {{ $balance >= 0 ? 'text-[#22C55E]' : 'text-[#EF4444]' }}">৳{{ number_format($balance, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-[#22C55E]/5">
                        <span class="text-xs text-[#94A3B8]">Total Income</span>
                        <span class="text-sm font-bold text-[#22C55E]">৳{{ number_format($totalIncome, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-2 py-1.5 rounded-lg bg-[#EF4444]/5">
                        <span class="text-xs text-[#94A3B8]">Total Expense</span>
                        <span class="text-sm font-bold text-[#EF4444]">৳{{ number_format($totalExpense, 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between px-2 text-xs text-[#94A3B8]">
                        <span>This month</span>
                        <span><strong class="text-[#22C55E]">+৳{{ number_format($monthIncome, 0) }}</strong> / <strong class="text-[#EF4444]">-৳{{ number_format($monthExpense, 0) }}</strong></span>
                    </div>
                    <div class="flex items-center justify-between px-2 text-xs text-[#94A3B8]">
                        <span>Today</span>
                        <span><strong class="text-[#22C55E]">+৳{{ number_format($todayIncome, 0) }}</strong> / <strong class="text-[#EF4444]">-৳{{ number_format($todayExpense, 0) }}</strong></span>
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Row 3: Website + SEO + Tracking + Users --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Website --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Website</h2>
                    <a href="{{ admin_route('website.dashboard') }}" class="text-xs text-[#3B82F6] hover:underline">Manage</a>
                </div>
                <div class="space-y-2">
                    <x-mini-stat icon="project-diagram" color="#3B82F6" label="Projects" :value="$totalWebsiteProjects" :sub="$activeWebsiteProjects . ' active'" />
                    <x-mini-stat icon="folder" color="#22C55E" label="Categories" :value="$totalWebsiteCategories" :sub="$activeWebsiteCategories . ' active'" />
                    <x-mini-stat icon="question-circle" color="#F59E0B" label="FAQs" :value="$faqCount" />
                    <x-mini-stat icon="star" color="#A855F7" label="Testimonials" :value="$testimonialCount" />
                </div>
            </div>

            {{-- SEO --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">SEO</h2>
                    <a href="{{ admin_route('seo.index') }}" class="text-xs text-[#3B82F6] hover:underline">Manage</a>
                </div>
                <div class="space-y-2">
                    <x-mini-stat icon="tag" color="#3B82F6" label="SEO Entries" :value="$seoMetaCount" />
                    <x-mini-stat icon="random" color="#F59E0B" label="Active Redirects" :value="$activeRedirects" :sub="$totalRedirects . ' total'" />
                    <x-mini-stat icon="mouse-pointer" color="#22C55E" label="Redirect Hits" :value="number_format($redirectHits)" />
                    <x-mini-stat icon="search" color="#A855F7" label="SEO Settings" value="Configured" />
                </div>
            </div>
            @endif

            {{-- Tracking --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Tracking</h2>
                    <a href="{{ admin_route('tracking.index') }}" class="text-xs text-[#3B82F6] hover:underline">Manage</a>
                </div>
                <div class="space-y-2">
                    <x-mini-stat icon="broadcast-tower" color="#3B82F6" label="Active Pixels" :value="$activePixels" :sub="$totalPixels . ' total'" />
                    <x-mini-stat icon="vial" color="#F59E0B" label="Diagnostics" value="Test" :href="admin_route('tracking.diagnostics')" />
                    <x-mini-stat icon="list" color="#22C55E" label="Event Log" value="View" :href="admin_route('monitoring.index')" />
                </div>
            </div>
            @endif

            {{-- Users & System --}}
            @if (Auth::user()->role === 'superadmin')
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">System</h2>
                    <a href="{{ admin_route('workers.index') }}" class="text-xs text-[#3B82F6] hover:underline">Manage</a>
                </div>
                <div class="space-y-2">
                    <x-mini-stat icon="users" color="#A855F7" label="Workers" :value="$totalWorkers" :sub="$activeUsers . ' active'" />
                    <x-mini-stat icon="user-friends" color="#3B82F6" label="Registered Clients" :value="$totalClients" />
                    <x-mini-stat icon="envelope" color="#EF4444" label="Inquiries" :value="$totalInquiries" :sub="$unreadInquiries . ' unread'" :href="admin_route('inquiries.index')" />
                    <x-mini-stat icon="images" color="#F59E0B" label="Pending Images" :value="$pendingImages" />
                </div>
            </div>
            @endif
        </div>

        {{-- Row 4: Recent Activity + Quick Links --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Activity Log --}}
            @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
            <div class="lg:col-span-2 rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-[#E6EDF3]">Recent Activity</h2>
                    <a href="{{ admin_route('monitoring.index') }}" class="rounded-lg bg-[#3B82F6]/10 px-3 py-1.5 text-xs font-medium text-[#3B82F6] hover:bg-[#3B82F6]/20 transition-colors">View All</a>
                </div>
                <div class="divide-y divide-[#232A36] max-h-72 overflow-y-auto custom-scrollbar">
                    @forelse ($recentLogs as $log)
                    <div class="flex items-center gap-3 px-1 py-2.5">
                        <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-[#3B82F6]/10">
                            <i class="fas fa-clock text-[10px] text-[#3B82F6]"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-[#E6EDF3] truncate">{{ $log->action }}</p>
                            <p class="text-xs text-[#6B7280] truncate">{{ $log->description }} · <span class="text-[#94A3B8]">{{ $log->created_at->diffForHumans() }}</span></p>
                        </div>
                        <span class="text-[10px] uppercase px-1.5 py-0.5 rounded bg-[#232A36] text-[#94A3B8]">{{ $log->module }}</span>
                    </div>
                    @empty
                    <div class="py-8 text-center text-sm text-[#94A3B8]">No recent activity.</div>
                    @endforelse
                </div>
            </div>
            @endif

            {{-- Quick Actions --}}
            <div class="rounded-xl border border-[#232A36] bg-[#161B22] p-4">
                <h2 class="text-sm font-semibold text-[#E6EDF3] mb-3">Shortcuts</h2>
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ admin_route('orders.index') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #F59E0B15;">
                        <i class="fas fa-cart-shopping text-lg" style="color: #F59E0B;"></i>
                        <span class="text-xs text-[#94A3B8]">Orders</span>
                    </a>
                    <a href="{{ admin_route('orders.create') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #22C55E15;">
                        <i class="fas fa-plus-circle text-lg" style="color: #22C55E;"></i>
                        <span class="text-xs text-[#94A3B8]">Add Orders</span>
                    </a>
                    <a href="{{ admin_route('stock.in') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #3B82F615;">
                        <i class="fas fa-warehouse text-lg" style="color: #3B82F6;"></i>
                        <span class="text-xs text-[#94A3B8]">Stock In</span>
                    </a>
                    <a href="{{ admin_route('stock.out') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #EF444415;">
                        <i class="fas fa-right-left text-lg" style="color: #EF4444;"></i>
                        <span class="text-xs text-[#94A3B8]">Stock Out</span>
                    </a>
                    <a href="{{ admin_route('finance.dashboard') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #14B8A615;">
                        <i class="fas fa-wallet text-lg" style="color: #14B8A6;"></i>
                        <span class="text-xs text-[#94A3B8]">Finance</span>
                    </a>
                    <a href="{{ admin_route('finance.transactions') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #8B5CF615;">
                        <i class="fas fa-money-bill-transfer text-lg" style="color: #8B5CF6;"></i>
                        <span class="text-xs text-[#94A3B8]">Transactions</span>
                    </a>
                    <a href="{{ admin_route('seo.dashboard') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #06B6D415;">
                        <i class="fas fa-magnifying-glass-chart text-lg" style="color: #06B6D4;"></i>
                        <span class="text-xs text-[#94A3B8]">SEO</span>
                    </a>
                    <a href="{{ admin_route('tracking.index') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #EC489915;">
                        <i class="fas fa-chart-line text-lg" style="color: #EC4899;"></i>
                        <span class="text-xs text-[#94A3B8]">Tracking</span>
                    </a>
                    <a href="{{ admin_route('workers.index') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #A855F715;">
                        <i class="fas fa-users text-lg" style="color: #A855F7;"></i>
                        <span class="text-xs text-[#94A3B8]">Users</span>
                    </a>
                    <a href="{{ admin_route('website.dashboard') }}" class="flex flex-col items-center justify-center gap-1.5 rounded-lg py-3 transition-colors hover:brightness-125" style="background-color: #6366F115;">
                        <i class="fas fa-paint-roller text-lg" style="color: #6366F1;"></i>
                        <span class="text-xs text-[#94A3B8]">Website Customization</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateDateTime() {
            const now = new Date();
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
            const day = days[now.getDay()];
            const date = now.getDate();
            const month = months[now.getMonth()];
            const year = now.getFullYear();
            let hours = now.getHours();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12 || 12;
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('dashboard-datetime').textContent = `${day}, ${date} ${month} ${year} - ${hours}:${minutes} ${ampm}`;
        }
        updateDateTime();
        setInterval(updateDateTime, 60000);

        // Auto-refresh KPI cards every 60 seconds
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.html) {
                    document.getElementById('kpi-cards-grid').innerHTML = data.html;
                }
            })
            .catch(() => {});
        }, 60000);
    </script>
    @endpush
</x-layouts.app>
