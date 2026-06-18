@props(['logs', 'columns'])

<div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead>
            <tr class="border-b border-[#232A36]">
                @foreach($columns as $key => $label)
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-[#94A3B8]">{{ $label }}</th>
                @endforeach
                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-[#94A3B8]">Description</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#232A36]">
            @forelse($logs as $log)
                <tr class="transition-colors hover:bg-[#1C2333]">
                    @foreach($columns as $key => $label)
                        <td class="whitespace-nowrap px-6 py-4 text-[#E6EDF3]">{{ $log->$key ?? 'N/A' }}</td>
                    @endforeach
                    <td class="px-6 py-4 text-[#94A3B8] max-w-[300px] truncate" title="{{ $log->action }} performed">
                        {{ $log->action }} action performed.
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($columns) + 1 }}" class="px-6 py-12 text-center text-sm text-[#94A3B8]">No logs found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if(method_exists($logs, 'links'))
        <div class="p-4 border-t border-[#232A36]">
            {{ $logs->links() }}
        </div>
    @endif
</div>
