<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('admin_route')) {
    function admin_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        if (array_is_list($parameters)) {
            array_unshift($parameters, Auth::user()?->role);
        } else {
            $parameters['role'] = Auth::user()?->role;
        }
        return route($name, $parameters, $absolute);
    }
}
