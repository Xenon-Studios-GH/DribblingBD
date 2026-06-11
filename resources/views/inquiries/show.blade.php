<x-layouts.app title="Inquiry Details">
    <div>
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-[#E6EDF3]">Inquiry Details</h1>
                <p class="mt-1 text-sm text-[#94A3B8]">From {{ $inquiry->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ admin_route('inquiries.index') }}"
                   class="flex items-center gap-2 rounded-xl border border-[#232A36] bg-[#161B22] px-4 py-2.5 text-sm font-medium text-[#94A3B8] hover:text-[#E6EDF3] transition-colors">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <form action="{{ admin_route('inquiries.destroy', $inquiry) }}" method="POST"
                      onsubmit="return confirm('Delete this inquiry?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center gap-2 rounded-xl bg-[#EF4444]/10 px-4 py-2.5 text-sm font-medium text-[#EF4444] hover:bg-[#EF4444]/20 transition-colors">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-card>
                    <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Details</h3>
                    <p class="whitespace-pre-wrap text-sm text-[#94A3B8] leading-relaxed">
                        {{ $inquiry->details ?: 'No details provided.' }}
                    </p>
                </x-card>
            </div>

            <div class="space-y-4">
                <x-card>
                    <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Contact Information</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-[10px] font-medium uppercase tracking-wider text-[#6B7280]">Name</label>
                            <p class="text-sm text-[#E6EDF3]">{{ $inquiry->name }}</p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase tracking-wider text-[#6B7280]">Phone</label>
                            <p class="text-sm text-[#E6EDF3]">
                                <a href="tel:{{ $inquiry->phone }}" class="text-[#3B82F6] hover:underline">{{ $inquiry->phone }}</a>
                            </p>
                        </div>
                        <div>
                            <label class="text-[10px] font-medium uppercase tracking-wider text-[#6B7280]">Submitted</label>
                            <p class="text-sm text-[#E6EDF3]">{{ $inquiry->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                    </div>
                </x-card>

                @if ($inquiry->image)
                <x-card>
                    <h3 class="mb-4 text-sm font-semibold text-[#E6EDF3]">Attached Image</h3>
                    <a href="{{ asset('storage/' . $inquiry->image) }}" target="_blank"
                       class="block overflow-hidden rounded-lg border border-[#232A36]">
                        <img src="{{ asset('storage/' . $inquiry->image) }}" alt="Inquiry image"
                             class="h-auto w-full object-cover">
                    </a>
                </x-card>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
