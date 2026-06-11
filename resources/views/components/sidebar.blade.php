@props(['active' => 'dashboard'])

<aside class="fixed left-0 top-0 z-40 flex h-screen flex-col border-r border-[#232A36] bg-[#0F1117] transition-all duration-300 w-64 -translate-x-full md:translate-x-0" :class="{'translate-x-0': sidebarOpen}">

    <!-- Logo -->
    <div class="flex min-h-14 shrink-0 items-center border-b border-[#232A36] lg:px-5">
        <span class="text-lg font-bold tracking-tight"><span class="text-white">Dribbling</span><span class="text-[#E85D2C]">BD</span></span>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 space-y-0.5 overflow-y-auto px-1.5 py-3 lg:px-2" aria-label="Main navigation">
        @if (Auth::user()->role !== 'staff')
        <x-nav-link href="{{ admin_route('dashboard') }}" :active="request()->routeIs('dashboard')" icon="dashboard">
            Dashboard
        </x-nav-link>
        @endif

        @if (Auth::user()->role === 'superadmin')
        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Administration</p>

        <x-nav-link href="{{ admin_route('workers.index') }}" :active="request()->routeIs('workers.*')" icon="users">
            Workers
        </x-nav-link>
        <x-nav-link href="{{ admin_route('login-logs.index') }}" :active="request()->routeIs('login-logs.*')" icon="login">
            Login Logs
        </x-nav-link>
        <x-nav-link href="{{ admin_route('work-logs.index') }}" :active="request()->routeIs('work-logs.*')" icon="activity">
            Work Logs
        </x-nav-link>
        <x-nav-link href="{{ admin_route('inquiries.index') }}" :active="request()->routeIs('inquiries.*')" icon="inquiry">
            Inquiries
        </x-nav-link>
        @endif

        @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">SEO</p>

        <x-nav-link href="{{ admin_route('seo.index') }}" :active="request()->routeIs('seo*')" icon="seo">
            SEO Management
        </x-nav-link>
        @endif

        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Stock</p>

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
        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Orders</p>

        <x-nav-link href="{{ admin_route('orders.index') }}" :active="request()->routeIs('orders.*')" icon="order">
            All Orders
        </x-nav-link>
        <x-nav-link href="{{ admin_route('orders.create') }}" :active="request()->routeIs('orders.create')" icon="plus">
            New Order
        </x-nav-link>

        @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Finance</p>

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
        <div class="my-2 border-t border-[#232A36]"></div>
        <p class="px-2 pb-0.5 text-[10px] font-medium uppercase tracking-wider text-[#94A3B8]">Website</p>

        <x-nav-link href="{{ admin_route('website.dashboard') }}" :active="request()->routeIs('website.dashboard')" icon="chart">
            Dashboard
        </x-nav-link>
        <x-nav-link href="{{ admin_route('website.projects') }}" :active="request()->routeIs('website.projects*')" icon="project">
            Projects
        </x-nav-link>
        <x-nav-link href="{{ admin_route('website.categories') }}" :active="request()->routeIs('website.categories*')" icon="category">
            Categories
        </x-nav-link>
        <x-nav-link href="{{ admin_route('website.customization.index') }}" :active="request()->routeIs('website.customization*')" icon="cog">
            Website Customization
        </x-nav-link>
        @endif
    </nav>

</aside>
