<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRoleMatches
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user || !$user->status) {
            abort(403, 'Unauthorized access.');
        }
        $urlRole = $request->route('role');
        if (!$urlRole || $user->role !== $urlRole) {
            abort(403, 'Unauthorized access.');
        }
        return $next($request);
    }
}
