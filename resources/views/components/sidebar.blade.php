@props(['active' => 'dashboard'])

<aside class="fixed left-0 top-0 z-40 flex h-screen flex-col border-r border-[#232A36] bg-[#0F1117] transition-all duration-300
              w-64
              -translate-x-full md:translate-x-0"
    :class="{'translate-x-0': sidebarOpen}">

    <!-- Logo -->
    <div class="flex h-16 shrink-0 items-center gap-3 border-b border-[#232A36] px-4 lg:px-6">
        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#3B82F6]">
            <i class="fas fa-cubes text-2xl text-white"></i>
        </div>
        <span class="text-lg font-bold text-[#E6EDF3]">Dribbling Stock</span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-2 py-4 lg:px-3" aria-label="Main navigation">
        @if (Auth::user()->role !== 'staff')
        <x-nav-link href="{{ admin_route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
            Dashboard
        </x-nav-link>
        @endif

        @if (Auth::user()->role === 'superadmin')
        <div class="my-3 border-t border-[#232A36]"></div>
        <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Administration</p>

        <x-nav-link href="{{ admin_route('workers.index') }}" :active="request()->routeIs('workers.*')" icon="users">
            Workers
        </x-nav-link>
        <x-nav-link href="{{ admin_route('login-logs.index') }}" :active="request()->routeIs('login-logs.*')" icon="login">
            Login Logs
        </x-nav-link>
        <x-nav-link href="{{ admin_route('work-logs.index') }}" :active="request()->routeIs('work-logs.*')" icon="activity">
            Work Logs
        </x-nav-link>
        @endif

        <div class="my-3 border-t border-[#232A36]"></div>
        <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Stock</p>

        <x-nav-link href="{{ admin_route('stock.management') }}" :active="request()->routeIs('stock.management')" icon="stock">
            Stock Management
        </x-nav-link>
        @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
        <x-nav-link href="{{ admin_route('products.create') }}" :active="request()->routeIs('products.create')" icon="plus">
            Add Product
        </x-nav-link>
        @endif
        @if (Auth::user()->role !== 'staff')
        <x-nav-link href="{{ admin_route('stock.activity') }}" :active="request()->routeIs('stock.activity')" icon="activity">
            Recent Activity
        </x-nav-link>
        @endif
        <div class="my-3 border-t border-[#232A36]"></div>
        <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Orders</p>

        <x-nav-link href="{{ admin_route('orders.index') }}" :active="request()->routeIs('orders.*')" icon="order">
            All Orders
        </x-nav-link>
        <x-nav-link href="{{ admin_route('orders.create') }}" :active="request()->routeIs('orders.create')" icon="plus">
            New Order
        </x-nav-link>

        @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
        <div class="my-3 border-t border-[#232A36]"></div>
        <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Finance</p>

        <x-nav-link href="{{ admin_route('finance.dashboard') }}" :active="request()->routeIs('finance.dashboard')" icon="chart">
            Dashboard
        </x-nav-link>
        <x-nav-link href="{{ admin_route('finance.transactions') }}" :active="request()->routeIs('finance.transactions*')" icon="transaction">
            Transactions
        </x-nav-link>
        <x-nav-link href="{{ admin_route('finance.categories') }}" :active="request()->routeIs('finance.categories*')" icon="category">
            Categories
        </x-nav-link>
        <x-nav-link href="{{ admin_route('finance.reports') }}" :active="request()->routeIs('finance.reports')" icon="report">
            Reports
        </x-nav-link>
        @endif
        @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
        <div class="my-3 border-t border-[#232A36]"></div>
        <p class="px-3 pb-1 text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Website</p>

        <x-nav-link href="{{ admin_route('website.dashboard') }}" :active="request()->routeIs('website.dashboard')" icon="chart">
            Dashboard
        </x-nav-link>
        <x-nav-link href="{{ admin_route('website.projects') }}" :active="request()->routeIs('website.projects*')" icon="project">
            Projects
        </x-nav-link>
        <x-nav-link href="{{ admin_route('website.categories') }}" :active="request()->routeIs('website.categories*')" icon="category">
            Categories
        </x-nav-link>
        @endif
    </nav>

</aside>