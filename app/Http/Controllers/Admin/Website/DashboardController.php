<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteCategory;
use App\Models\WebsiteProject;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $totalProjects = WebsiteProject::count();
        $activeProjects = WebsiteProject::active()->count();
        $projectsWithImages = WebsiteProject::has('images')->count();
        $projectsMissingImages = $totalProjects - $projectsWithImages;

        $categories = WebsiteCategory::withCount('projects')->orderBy('name')->get();

        return view('website.dashboard', compact(
            'totalProjects', 'activeProjects',
            'projectsWithImages', 'projectsMissingImages',
            'categories'
        ));
    }
}
