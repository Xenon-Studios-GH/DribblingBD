<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginTrap;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    const MODULES = [
        'all'     => ['system', 'user', 'order', 'stock', 'finance', 'website', 'seo', 'inquiry'],
        'login'   => ['system'],
        'orders'  => ['order'],
        'stock'   => ['stock'],
        'finance' => ['finance'],
        'web'     => ['website', 'seo'],
    ];

    public function index(Request $request)
    {
        if ($request->tab === 'traps') {
            $traps = LoginTrap::latest('trapped_at')->paginate(20);
            if ($request->ajax()) {
                return view('monitoring._traps_table', compact('traps'));
            }
            $users = User::whereHas('workLogs')->orderBy('name')->get(['id', 'name']);
            $logs = $this->fetchLogs($request);
            return view('monitoring.index', compact('users', 'logs', 'traps'));
        }

        if ($request->ajax()) {
            $logs = $this->fetchLogs($request);
            return view('monitoring._table', compact('logs'));
        }

        $users = User::whereHas('workLogs')->orderBy('name')->get(['id', 'name']);
        $logs = $this->fetchLogs($request);

        return view('monitoring.index', compact('users', 'logs'));
    }

    private function fetchLogs(Request $request, int $perPage = 20)
    {
        $modules = self::MODULES[$request->tab] ?? self::MODULES['all'];

        return WorkLog::with('user')
            ->when($modules, function ($q) use ($modules) {
                $q->whereIn('module', $modules);
            })
            ->when($request->search, function ($q, $s) {
                $q->where(function ($q) use ($s) {
                    $q->where('action', 'like', "%{$s}%")
                      ->orWhere('description', 'like', "%{$s}%")
                      ->orWhere('module', 'like', "%{$s}%");
                });
            })
            ->when($request->user_id, function ($q, $id) {
                $q->where('user_id', $id);
            })
            ->when($request->module, function ($q, $m) {
                $q->where('module', $m);
            })
            ->when($request->date_from, function ($q, $d) {
                $q->whereDate('created_at', '>=', $d);
            })
            ->when($request->date_to, function ($q, $d) {
                $q->whereDate('created_at', '<=', $d);
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }
}
