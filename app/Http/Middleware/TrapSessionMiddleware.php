<?php

namespace App\Http\Middleware;

use App\Models\LoginTrap;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrapSessionMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->has('trap_session')) {
            $isExpired = $request->session()->get('trap_triggered_at', 0) < now()->subDays(7)->timestamp;

            if ($isExpired) {
                $request->session()->forget('trap_session');
                $request->session()->forget('trap_triggered_at');
                return redirect()->route('authentication');
            }

            $ip = $request->ip();
            $activeTrap = LoginTrap::forIp($ip)->active()->first();
            if (!$activeTrap) {
                $request->session()->forget('trap_session');
                $request->session()->forget('trap_triggered_at');
                return redirect()->route('authentication');
            }

            return redirect()->route('trap.page');
        }

        return $next($request);
    }
}
