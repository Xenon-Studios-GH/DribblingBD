<?php

namespace App\Http\Middleware;

use App\Models\SeoRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSeoRedirects
{
    protected array $except = [
        'up',
        'uploads/*',
        '__tracking/*',
        'build/*',
        '_debugbar/*',
        'storage/*',
        'images/*',
        'favicon.ico',
        'robots.txt',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->except as $pattern) {
            if ($request->is($pattern)) {
                return $next($request);
            }
        }

        if ($request->expectsJson() || $request->ajax()) {
            return $next($request);
        }

        $path = $request->path();

        $redirects = cache()->remember('seo.redirects.active', 3600, function () {
            return SeoRedirect::active()
                ->orderByRaw('LENGTH(from_url) DESC')
                ->get(['from_url', 'to_url', 'status_code', 'match_type', 'id']);
        });

        foreach ($redirects as $redirect) {
            $match = match ($redirect->match_type) {
                'exact' => $path === ltrim($redirect->from_url, '/'),
                'prefix' => str_starts_with($path, ltrim($redirect->from_url, '/')),
                'regex' => preg_match($redirect->from_url, $path) === 1,
                default => false,
            };

            if ($match) {
                $redirect->increment('hits', 1, ['last_hit_at' => now()]);

                return redirect()->to($redirect->to_url, $redirect->status_code);
            }
        }

        return $next($request);
    }
}
