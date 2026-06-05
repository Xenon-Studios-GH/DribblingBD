@if (!request()->routeIs('shop.products.index') && !request()->routeIs('shop.profile.*'))
<nav x-data="{ lastScroll: 0, hidden: false }" x-init="window.addEventListener('scroll', () => { const cur = window.scrollY; hidden = cur > lastScroll ? true : (cur < lastScroll ? false : hidden); lastScroll = cur; }, { passive: true })" class="hidden lg:block bg-white border-b border-gray-200">
    <div class="overflow-hidden transition-all duration-300 max-h-0" :class="!hidden && 'max-h-20'">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-1 overflow-x-auto py-0">
                @php
                    $currentType = request('type', 'all');
                @endphp
                <a href="{{ route('shop.products.index', ['type' => 'all']) }}" class="whitespace-nowrap px-4 py-3 text-sm font-medium transition-colors border-b-2 {{ $currentType === 'all' ? 'text-[#E85D2C] border-[#E85D2C]' : 'text-gray-600 border-transparent hover:text-[#E85D2C] hover:border-[#E85D2C]' }}">
                    All Jerseys
                </a>
                <a href="{{ route('shop.products.index', ['type' => 'player']) }}" class="whitespace-nowrap px-4 py-3 text-sm font-medium transition-colors border-b-2 {{ $currentType === 'player' ? 'text-[#E85D2C] border-[#E85D2C]' : 'text-gray-600 border-transparent hover:text-[#E85D2C] hover:border-[#E85D2C]' }}">
                    Player Edition
                </a>
                <a href="{{ route('shop.products.index', ['type' => 'fan']) }}" class="whitespace-nowrap px-4 py-3 text-sm font-medium transition-colors border-b-2 {{ $currentType === 'fan' ? 'text-[#E85D2C] border-[#E85D2C]' : 'text-gray-600 border-transparent hover:text-[#E85D2C] hover:border-[#E85D2C]' }}">
                    Fan Edition
                </a>
            </div>
        </div>
    </div>
</nav>
@endif