<?php

use Illuminate\Support\Facades\Auth;

if (!function_exists('shop_project_url')) {
    function shop_project_url($project)
    {
        $cat = $project->category;
        if (!$cat) {
            return '#';
        }
        $categorySlug = $cat->parent?->slug ?? $cat->slug;
        $subcategorySlug = $cat->slug;
        return route('shop.project.detail', [
            'categorySlug' => $categorySlug,
            'subcategorySlug' => $subcategorySlug,
            'projectSlug' => $project->slug,
        ]);
    }
}

if (!function_exists('admin_route')) {
    function admin_route(string $name, $parameters = [], bool $absolute = true): string
    {
        if (!is_array($parameters)) {
            $parameters = [$parameters];
        }
        $role = Auth::user()?->role ?? 'admin';
        if (array_is_list($parameters)) {
            array_unshift($parameters, $role);
        } else {
            $parameters['role'] = $role;
        }
        return route($name, $parameters, $absolute);
    }
}
