@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#0F1117] text-[#E6EDF3] antialiased">
    <div class="flex min-h-screen" x-data="appLayout()" @keydown.escape.window="sidebarOpen = false">
        <x-sidebar />

        <div class="flex flex-1 flex-col md:ml-16 lg:ml-64">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-[#232A36] bg-[#0F1117] px-4 md:px-8"
                x-data="{ scrolled: false }"
                @scroll.window.passive="scrolled = window.scrollY > 0"
                :class="scrolled && 'shadow-lg shadow-black/10'">
                <button @click="sidebarOpen = true" class="flex md:hidden h-10 w-10 items-center justify-center rounded-xl text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3]" aria-label="Open menu">
                    <i class="fas fa-bars h-5 w-5"></i>
                </button>

                <h1 class="text-lg font-semibold text-[#E6EDF3]">{{ $title }}</h1>

                <div class="flex-1"></div>

                <a href="{{ url('/') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors" aria-label="Landing page">
                    <i class="fas fa-globe h-4 w-4"></i>
                    <span class="hidden md:inline">Landing Page</span>
                </a>

                @if (in_array(Auth::user()->role, ['superadmin', 'admin']))
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <button @click="toggle()" class="relative flex h-10 w-10 items-center justify-center rounded-xl text-[#94A3B8] hover:bg-[#1C2333] hover:text-[#E6EDF3]" aria-label="Notifications">
                        <i class="fas fa-bell h-5 w-5"></i>
                        <span x-show="unreadCount > 0" x-cloak
                            class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-[#EF4444] text-[10px] font-bold text-white"
                            x-text="unreadCount"></span>
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                        class="absolute right-0 mt-2 w-80 rounded-xl border border-[#232A36] bg-[#161B22] py-2 shadow-xl max-h-96 overflow-y-auto">
                        <div class="flex items-center justify-between px-4 pb-2 border-b border-[#232A36]">
                            <span class="text-sm font-semibold text-[#E6EDF3]">Notifications</span>
                            <a href="{{ admin_route('finance.notifications') }}" class="text-xs text-[#3B82F6] hover:underline">View all</a>
                        </div>
                        <template x-for="notif in notifications" :key="notif.id">
                            <div class="px-4 py-3 hover:bg-[#1C2333] cursor-pointer border-b border-[#232A36]/50"
                                :class="{'opacity-60': notif.is_read}"
                                @click="markRead(notif)">
                                <p class="text-sm font-medium text-[#E6EDF3]" x-text="notif.title"></p>
                                <p class="text-xs text-[#94A3B8] mt-0.5" x-text="notif.message"></p>
                                <p class="text-[10px] text-[#64748B] mt-1" x-text="notif.time_ago"></p>
                            </div>
                        </template>
                        <div x-show="notifications.length === 0" class="px-4 py-8 text-center text-sm text-[#94A3B8]">
                            No notifications
                        </div>
                    </div>
                </div>
                @endif

                <div x-data="{ dropdownOpen: false }" @keydown.escape.window="dropdownOpen = false" class="relative">
                    <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-[#E6EDF3] hover:bg-[#1C2333]" aria-label="User menu" aria-haspopup="true" :aria-expanded="dropdownOpen">
                        <span class="hidden md:inline text-sm">{{ Auth::user()->name }}</span>
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#3B82F6]">
                            <span class="text-xs font-medium text-white">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                        </div>
                    </button>
                    <div x-show="dropdownOpen" @click.outside="dropdownOpen = false" x-transition class="absolute right-0 mt-2 w-48 rounded-xl border border-[#232A36] bg-[#161B22] py-1 shadow-xl" role="menu">
                        <div class="border-b border-[#232A36] px-4 py-3">
                            <p class="text-sm font-medium text-[#E6EDF3]">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-[#94A3B8]">{{ ucfirst(Auth::user()->role) }}</p>
                        </div>
                        <div class="px-2 pt-1">
                            <a href="{{ url('/') }}" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-sm text-[#E6EDF3] hover:bg-[#1C2333]" role="menuitem">
                                <i class="fas fa-globe h-4 w-4"></i>
                                Landing Page
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                                @csrf
                                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-2 py-2 text-sm text-[#EF4444] hover:bg-[#1C2333]" role="menuitem">
                                    <i class="fas fa-sign-out-alt h-4 w-4"></i>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 md:p-8">
                {{ $slot }}
            </main>
        </div>

        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-300"
            x-transition:leave="transition-opacity duration-200"
            class="fixed inset-0 z-30 bg-black/50 md:hidden"
            @click="sidebarOpen = false"
            aria-hidden="true"></div>
    </div>

    <script>
        function appLayout() {
            return {
                sidebarOpen: false,
            }
        }

        function notificationBell() {
            return {
                open: false,
                unreadCount: 0,
                notifications: [],
                init() {
                    this.fetchUnreadCount();
                    this.fetchNotifications();
                    setInterval(() => this.fetchUnreadCount(), 30000);
                },
                fetchUnreadCount() {
                    fetch('{{ route('finance.notifications.unread', ['role' => Auth::user()->role]) }}')
                        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                        .then(d => { this.unreadCount = d.count; })
                        .catch(e => console.error('Failed to fetch unread count:', e));
                },
                fetchNotifications() {
                    fetch('{{ route('finance.notifications.unread', ['role' => Auth::user()->role]) }}?limit=5')
                        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                        .then(d => { this.notifications = d.notifications || []; })
                        .catch(e => console.error('Failed to fetch notifications:', e));
                },
                toggle() {
                    this.open = !this.open;
                    if (this.open) this.fetchNotifications();
                },
                markRead(notif) {
                    if (!notif.is_read) {
                        fetch('{{ route('finance.notifications.read', ['role' => Auth::user()->role, 'notification' => '__ID__']) }}'.replace('__ID__', notif.id), { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                            .catch(e => console.error('Failed to mark notification as read:', e));
                        notif.is_read = true;
                        this.unreadCount = Math.max(0, this.unreadCount - 1);
                    }
                },
            };
        }
    </script>
    @stack('scripts')
</body>

</html>