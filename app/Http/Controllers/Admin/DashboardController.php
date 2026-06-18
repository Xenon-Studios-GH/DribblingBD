<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __invoke(DashboardService $dashboard)
    {
        $data = $dashboard->getDashboardData();

        if (request()->wantsJson()) {
            return view('dashboard.partials._kpi-cards', $dashboard->getKpiCardsData());
        }

        return view('dashboard.index', $data);
    }
}
