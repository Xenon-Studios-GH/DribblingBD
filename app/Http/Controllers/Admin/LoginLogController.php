<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoginLogService;
use Illuminate\Http\Request;

class LoginLogController extends Controller
{
    protected LoginLogService $loginLogService;

    public function __construct(LoginLogService $loginLogService)
    {
        $this->loginLogService = $loginLogService;
    }

    public function index(Request $request)
    {
        $filters = $request->only(['user_id', 'email', 'date_from', 'date_to', 'status']);
        $logs = $this->loginLogService->getLogs($filters);
        $users = User::whereIn('role', ['superadmin', 'admin', 'staff'])->orderBy('name')->get(['id', 'name', 'email']);

        return view('login-logs.index', compact('logs', 'users'));
    }
}
