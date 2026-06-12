@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 rounded-lg text-xs text-[#94A3B8] bg-[#161B22] border border-[#232A36]">Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-xs text-[#E6EDF3] bg-[#161B22] border border-[#232A36] hover:bg-[#1C2333] transition-colors">Previous</a>
            @endif
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-xs text-[#E6EDF3] bg-[#161B22] border border-[#232A36] hover:bg-[#1C2333] transition-colors">Next</a>
            @else
                <span class="px-3 py-2 rounded-lg text-xs text-[#94A3B8] bg-[#161B22] border border-[#232A36]">Next</span>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-xs text-[#94A3B8]">
                    Showing
                    @if ($paginator->firstItem())
                        <span class="font-medium text-[#E6EDF3]">{{ $paginator->firstItem() }}</span>
                        to
                        <span class="font-medium text-[#E6EDF3]">{{ $paginator->lastItem() }}</span>
                    @else
                        {{ $paginator->count() }}
                    @endif
                    of
                    <span class="font-medium text-[#E6EDF3]">{{ $paginator->total() }}</span>
                    results
                </p>
            </div>
            <div>
                <span class="relative z-0 inline-flex items-center gap-1">
                    @if ($paginator->onFirstPage())
                        <span class="px-3 py-2 rounded-lg text-xs text-[#4A5568] bg-[#161B22] border border-[#232A36] cursor-not-allowed">&laquo;</span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" class="px-3 py-2 rounded-lg text-xs text-[#94A3B8] bg-[#161B22] border border-[#232A36] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors">&laquo;</a>
                    @endif
                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span class="px-3 py-2 rounded-lg text-xs text-[#4A5568]">{{ $element }}</span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span class="px-3 py-2 rounded-lg text-xs font-medium text-white bg-[#3B82F6]">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-2 rounded-lg text-xs text-[#94A3B8] bg-[#161B22] border border-[#232A36] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors">{{ $page }}</a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" class="px-3 py-2 rounded-lg text-xs text-[#94A3B8] bg-[#161B22] border border-[#232A36] hover:bg-[#1C2333] hover:text-[#E6EDF3] transition-colors">&raquo;</a>
                    @else
                        <span class="px-3 py-2 rounded-lg text-xs text-[#4A5568] bg-[#161B22] border border-[#232A36] cursor-not-allowed">&raquo;</span>
                    @endif
                </span>
            </div>
        </div>
    </nav>
@endif
