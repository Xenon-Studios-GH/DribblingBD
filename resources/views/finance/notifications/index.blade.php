<x-layouts.app title="Notifications">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Notifications</h1>
            <form method="POST" action="{{ admin_route('finance.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="rounded-xl border border-[#232A36] px-4 py-2 text-sm text-[#94A3B8] hover:bg-[#1C2333]">
                    Mark All as Read
                </button>
            </form>
        </div>

        <x-card>
            @forelse($notifications as $n)
            <div class="flex items-start gap-3 py-3 border-b border-[#232A36] last:border-0 {{ $n->is_read ? 'opacity-60' : '' }}">
                <div class="flex-1">
                    <p class="text-sm font-medium text-[#E6EDF3]">{{ $n->title }}</p>
                    <p class="text-xs text-[#94A3B8] mt-0.5">{{ $n->message }}</p>
                    <p class="text-[10px] text-[#64748B] mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                @if(!$n->is_read)
                <form method="POST" action="{{ admin_route('finance.notifications.read', $n) }}">
                    @csrf
                    <button type="submit" class="text-xs text-[#3B82F6] hover:underline">Mark read</button>
                </form>
                @endif
            </div>
            @empty
            <p class="py-8 text-center text-sm text-[#94A3B8]">No notifications.</p>
            @endforelse
        </x-card>

        {{ $notifications->links() }}
    </div>
</x-layouts.app>
