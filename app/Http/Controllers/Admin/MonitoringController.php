<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WorkLog;
use Barryvdh\DomPDF\Facade\Pdf;
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
        if ($request->ajax()) {
            $logs = $this->fetchLogs($request);
            return view('monitoring._table', compact('logs'));
        }

        $users = User::whereHas('workLogs')->orderBy('name')->get(['id', 'name']);
        $logs = $this->fetchLogs($request);

        return view('monitoring.index', compact('users', 'logs'));
    }

    public function exportPdf(Request $request)
    {
        $logs = $this->fetchLogs($request, 9999);
        $filterLabel = $this->filterLabel($request);

        $pdf = Pdf::loadView('monitoring.pdf', compact('logs', 'filterLabel'));
        $pdf->setPaper('A4', 'landscape');

        return $pdf->download('monitoring-report.pdf');
    }

    private function filterLabel(Request $request): string
    {
        $tab = $request->tab ? ucfirst($request->tab) : 'All';
        $parts = [$tab . ' Logs'];
        if ($request->date_from && $request->date_to) {
            $parts[] = '(' . $request->date_from . ' — ' . $request->date_to . ')';
        } elseif ($request->date_from) {
            $parts[] = '(from ' . $request->date_from . ')';
        } elseif ($request->date_to) {
            $parts[] = '(until ' . $request->date_to . ')';
        }
        if ($request->search) {
            $parts[] = 'search: "' . e($request->search) . '"';
        }
        return implode(' ', $parts);
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
