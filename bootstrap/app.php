<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'role.match' => \App\Http\Middleware\EnsureRoleMatches::class,
        ]);

        $middleware->redirectGuestsTo(fn() => route('authentication'));

        $middleware->redirectUsersTo(fn() => route('dashboard', [
            'role' => auth()->user()?->role ?? 'admin',
        ]));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Render handlers
        $exceptions->render(function (HttpException $e, Request $request) {
            $status = $e->getStatusCode();
            if ($request->expectsJson()) {
                $message = $status >= 500 ? 'Server Error' : $e->getMessage();
                return response()->json(['error' => $message], $status);
            }
            if (view()->exists("errors.{$status}")) {
                return response()->view("errors.{$status}", [], $status);
            }
        });

        // Handle ModelNotFoundException
        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Resource not found.'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // Handle AuthenticationException
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }
            return redirect()->guest(route('authentication'));
        });

        // Handle ValidationException
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => $e->getMessage(), 'errors' => $e->errors()], 422);
            }
        });

        // Report all exceptions
        $exceptions->report(function (Throwable $e) {
            Log::error('Unhandled exception', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
        });
    })->create();
