<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\WorkLogService;
use Illuminate\Http\Request;

class UserLogController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index(Request $request)
    {
        $type = $request->query('type', 'work');
        $filters = $request->only(['user_id', 'module', 'action', 'search', 'date_from', 'date_to']);

        $logs = [];
        if ($type === 'work') {
            $logs = $this->workLogService->getLogs($filters);
        } elseif ($type === 'admin') {
            $logs = ActivityLog::latest()->paginate(20);
        } elseif ($type === 'clients') {
            $logs = User::getUniqueClients();
        }

        if ($request->ajax()) {
            return view('user-logs._table', compact('logs', 'type'));
        }

        return view('user-logs.index', compact('logs', 'type'));
    }
}
