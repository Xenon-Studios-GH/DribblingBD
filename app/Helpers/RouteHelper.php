<?php

if (!function_exists('shop_project_url')) {
    function shop_project_url($project)
    {
        if (!$project?->slug) {
            return '#';
        }
        return route('shop.products.show', $project);
    }
}

if (!function_exists('storage_url')) {
    function storage_url(?string $path): string
    {
        if (!$path) {
            return '';
        }

        return asset('uploads/' . $path);
    }
}

if (!function_exists('admin_route')) {
    function admin_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        return route($name, $parameters, $absolute);
    }
}
