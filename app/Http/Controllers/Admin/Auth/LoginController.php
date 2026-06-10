<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
use App\Services\WorkLogService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected LoginLogService $loginLogService;
    protected WorkLogService $workLogService;

    public function __construct(LoginLogService $loginLogService, WorkLogService $workLogService)
    {
        $this->loginLogService = $loginLogService;
        $this->workLogService = $workLogService;
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->status) {
            $this->loginLogService->recordLogin($request->email, false, $user->id);
            return back()->withErrors([
                'email' => 'Your account has been deactivated.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            if ($request->boolean('remember')) {
                config(['session.lifetime' => 40320]);
            }
            $request->session()->regenerate();

            $this->loginLogService->recordLogin($request->email, true, Auth::id());
            $this->workLogService->log('Login', 'system', Auth::id(), 'User logged in');

            $redirect = Auth::user()->role === 'customer' ? '/' : admin_route('dashboard');
            return redirect()->intended($redirect);
        }

        $this->loginLogService->recordLogin($request->email, false, $user?->id);
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
}
