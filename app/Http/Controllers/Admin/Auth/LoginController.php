<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
use App\Services\LoginThrottleService;
use App\Services\WorkLogService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    protected LoginLogService $loginLogService;
    protected WorkLogService $workLogService;
    protected LoginThrottleService $loginThrottleService;

    public function __construct(
        LoginLogService $loginLogService,
        WorkLogService $workLogService,
        LoginThrottleService $loginThrottleService
    ) {
        $this->loginLogService = $loginLogService;
        $this->workLogService = $workLogService;
        $this->loginThrottleService = $loginThrottleService;
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = $request->email;
        $ip = $request->ip();

        // Check throttle before attempting auth
        $throttle = $this->loginThrottleService->check($email, $ip);
        if (!$throttle['allowed']) {
            $minutes = ceil($throttle['remaining'] / 60);
            return back()->withErrors([
                'email' => "Too many attempts. Try again in {$minutes} minute(s).",
            ])->onlyInput('email');
        }

        $user = User::where('email', $email)->first();

        if ($user && !$user->status) {
            $this->loginLogService->recordLogin($email, false, $user->id);
            $this->loginThrottleService->increment($email, $ip);
            return back()->withErrors([
                'email' => 'Your account has been deactivated.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Check if this IP was recently trapped/locked
            if ($this->loginThrottleService->shouldTrapOnLogin($email, $ip)) {
                $this->loginThrottleService->triggerTrap($email, $ip, 'locked_ip_login');
                return $this->activateTrapSession($request);
            }

            $this->loginLogService->recordLogin($email, true, Auth::id());
            $this->workLogService->log('Login', 'system', Auth::id(), 'User logged in');
            $this->loginThrottleService->reset($email, $ip);

            $user = Auth::user();
            if ($user->role !== 'customer') {
                session()->flash('show_welcome', [
                    'name' => $user->name,
                    'role' => ucfirst($user->role),
                ]);
            }

            $redirect = $user->role === 'customer' ? '/' : admin_route('dashboard');
            return redirect()->intended($redirect);
        }

        // Failed auth
        $this->loginLogService->recordLogin($email, false, $user?->id);
        $result = $this->loginThrottleService->increment($email, $ip);

        // Check if trap triggered
        if ($result['trap_triggered']) {
            return $this->activateTrapSession($request);
        }

        return back()->withErrors([
            'email' => 'The email or password is incorrect.',
        ])->onlyInput('email');
    }

    protected function activateTrapSession(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->session()->flush();
        $request->session()->put('trap_session', true);
        $request->session()->put('trap_triggered_at', now()->timestamp);
        return redirect()->route('trap.page');
    }
}
