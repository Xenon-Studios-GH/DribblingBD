<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
use App\Services\WorkLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    protected LoginLogService $loginLogService;
    protected WorkLogService $workLogService;

    public function __construct(LoginLogService $loginLogService, WorkLogService $workLogService)
    {
        $this->loginLogService = $loginLogService;
        $this->workLogService = $workLogService;
    }

    public function __invoke(Request $request)
    {
        $userId = Auth::id();

        $this->workLogService->log('Logout', 'system', $userId, 'User logged out');
        $this->loginLogService->updateLogout($userId);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('authentication');
    }
}
