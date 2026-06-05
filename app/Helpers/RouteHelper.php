<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('admin_route')) {
    function admin_route(string $name, array $parameters = [], bool $absolute = true): string
    {
        return route($name, array_merge(['role' => Auth::user()?->role], $parameters), $absolute);
    }
}
