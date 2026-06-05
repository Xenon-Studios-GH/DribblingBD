<x-layouts.app title="Edit Worker">
    <div class="mx-auto max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-[#E6EDF3]">Edit Worker</h1>
            <p class="mt-1 text-sm text-[#94A3B8]">Update staff account details.</p>
        </div>

        <x-card>
            <form method="POST" action="{{ route('workers.update', $worker) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Full Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name', $worker->name) }}" required
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    @error('name') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $worker->email) }}" required
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    @error('email') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="phone" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Phone (optional)</label>
                    <input id="phone" type="text" name="phone" value="{{ old('phone', $worker->phone) }}"
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    @error('phone') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="role" class="mb-2 block text-sm font-medium text-[#E6EDF3]">Role</label>
                    <select id="role" name="role" required
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                        <option value="staff" {{ old('role', $worker->role) === 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="admin" {{ old('role', $worker->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role', $worker->role) === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                    @error('role') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-[#E6EDF3]">New Password (leave empty to keep current)</label>
                    <input id="password" type="password" name="password"
                        class="block w-full rounded-xl border border-[#232A36] bg-[#0F1117] px-4 py-2.5 text-sm text-[#E6EDF3] placeholder-[#94A3B8] transition-colors focus:border-[#3B82F6] focus:outline-none focus:ring-1 focus:ring-[#3B82F6]">
                    @error('password') <p class="mt-1 text-sm text-[#EF4444]">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#3B82F6] px-4 py-2.5 text-sm font-medium text-white transition-colors hover:bg-[#2563EB] focus:outline-none focus:ring-2 focus:ring-[#3B82F6] focus:ring-offset-2 focus:ring-offset-[#0F1117]">
                        Update Worker
                    </button>
                    <a href="{{ route('workers.index') }}" class="text-sm text-[#94A3B8] hover:text-[#E6EDF3]">Cancel</a>
                </div>
            </form>
        </x-card>
    </div>
</x-layouts.app>