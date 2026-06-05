<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\WorkLogService;
use Illuminate\Http\Request;

class WorkLogController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'module', 'action', 'date_from', 'date_to']);
        $logs = $this->workLogService->getLogs($filters);
        $users = User::whereIn('role', ['superadmin', 'admin', 'staff'])->orderBy('name')->get(['id', 'name', 'email']);

        return view('work-logs.index', compact('logs', 'users'));
    }
}
