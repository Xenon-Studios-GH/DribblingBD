<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WorkerController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index()
    {
        $workers = User::where('role', 'staff')->latest()->paginate(20);
        $admins = User::whereIn('role', ['superadmin', 'admin'])->latest()->paginate(20);
        return view('workers.index', compact('workers', 'admins'));
    }

    public function create()
    {
        return view('workers.create');
    }

    public function store(Request $request)
    {
        $allowedRoles = ['staff', 'admin'];
        if (auth()->user()->role === 'superadmin') {
            $allowedRoles[] = 'superadmin';
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = true;

        $user = User::create($validated);

        $this->workLogService->log(
            'User Created',
            'user',
            $user->id,
            "Worker {$user->name} ({$user->email}) was created"
        );

        return redirect(admin_route('workers.index'))
            ->with('success', 'Worker created successfully.');
    }

    public function edit(string $role, User $worker)
    {
        return view('workers.edit', compact('worker'));
    }

    public function update(Request $request, string $role, User $worker)
    {
        if ($worker->id === auth()->id() && $request->role !== $worker->role) {
            return redirect(admin_route('workers.index'))->with('error', 'You cannot change your own role.');
        }

        $allowedRoles = ['staff', 'admin'];
        if (auth()->user()->role === 'superadmin') {
            $allowedRoles[] = 'superadmin';
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($worker->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'role' => ['required', Rule::in($allowedRoles)],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $worker->update($validated);

        $this->workLogService->log(
            'User Updated',
            'user',
            $worker->id,
            "Worker {$worker->name} ({$worker->email}) was updated"
        );

        return redirect(admin_route('workers.index'))->with('success', 'Worker updated successfully.');
    }

    public function toggleStatus(string $role, User $worker)
    {
        if ($worker->id === auth()->id()) {
            return redirect(admin_route('workers.index'))->with('error', 'You cannot deactivate your own account.');
        }

        $newStatus = !$worker->status;
        $worker->update(['status' => $newStatus]);

        $action = $newStatus ? 'activated' : 'deactivated';
        $this->workLogService->log(
            'User Updated',
            'user',
            $worker->id,
            "Worker {$worker->name} ({$worker->email}) was {$action}"
        );

        return redirect(admin_route('workers.index'))->with('success', "Worker {$action} successfully.");
    }

}
