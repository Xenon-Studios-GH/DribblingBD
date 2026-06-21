<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PendingOrderTransaction;
use App\Models\WorkLog;
use Illuminate\Http\Request;

class AutomationController extends Controller
{
    public function __invoke(Request $request)
    {
        $auditLogs = WorkLog::where('module', 'Audit')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($log) {
                preg_match_all('/\[([A-Z0-9]+)\]/', $log->description ?? '', $checks);
                $issues = substr_count($log->action, 'issue');
                preg_match('/auto-fixed: (\d+)/', $log->action, $fixed);
                return [
                    'id' => $log->id,
                    'ran_at' => $log->created_at,
                    'summary' => $log->action,
                    'details' => $log->description,
                    'checks' => $checks[1] ?? [],
                    'issues' => $issues,
                    'fixed' => (int) ($fixed[1] ?? 0),
                ];
            });

        $pendingTransactions = PendingOrderTransaction::where('status', 'pending')->count();
        $pendingAmount = PendingOrderTransaction::where('status', 'pending')->sum('total_amount');

        $ordersByStatus = Order::selectRaw("
                status,
                COUNT(*) as count,
                COALESCE(SUM(total_amount), 0) as total,
                COALESCE(SUM(pending_payment), 0) as pending
            ")
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $lastCleanup = WorkLog::where('module', 'system')
            ->where('action', 'like', '%clean%')
            ->latest()
            ->value('created_at');

        $lastStockCheck = WorkLog::where('module', 'stock')
            ->latest()
            ->value('created_at');

        $schedulerFile = base_path('routes/console.php');
        $schedulerTasks = $this->parseSchedulerTasks($schedulerFile);

        $logFiles = [
            'audit' => $this->getLogFileInfo(storage_path('logs/audit.log')),
            'cleanup' => $this->getLogFileInfo(storage_path('logs/cleanup.log')),
            'laravel' => $this->getLogFileInfo(storage_path('logs/laravel.log')),
        ];

        return view('monitoring.automation', compact(
            'auditLogs',
            'pendingTransactions',
            'pendingAmount',
            'ordersByStatus',
            'lastCleanup',
            'lastStockCheck',
            'schedulerTasks',
            'logFiles',
        ));
    }

    private function parseSchedulerTasks(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        $content = file_get_contents($file);
        preg_match_all('/Schedule::command\(([^,]+)(?:, ([^)]+))?\)->([\w:]+)\(\)/', $content, $matches, PREG_SET_ORDER);

        $tasks = [];
        foreach ($matches as $m) {
            $command = trim($m[1], "'\" ");
            $args = isset($m[2]) ? trim($m[2], "'\" []") : '';
            $frequency = $m[3] ?? 'unknown';
            $name = class_basename(str_replace('::class', '', $command));
            $tasks[] = [
                'name' => $name ?: $command,
                'command' => $command,
                'args' => $args,
                'frequency' => $this->freqLabel($frequency),
            ];
        }

        return $tasks;
    }

    private function freqLabel(string $freq): string
    {
        return match ($freq) {
            'everyFiveMinutes' => 'Every 5 min',
            'everyTenMinutes' => 'Every 10 min',
            'everyFifteenMinutes' => 'Every 15 min',
            'everyThirtyMinutes' => 'Every 30 min',
            'hourly' => 'Hourly',
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            default => $freq,
        };
    }

    private function getLogFileInfo(string $path): array
    {
        if (!file_exists($path)) {
            return ['exists' => false, 'size' => 0, 'modified' => null];
        }
        return [
            'exists' => true,
            'size' => filesize($path),
            'size_formatted' => $this->formatBytes(filesize($path)),
            'modified' => date('Y-m-d H:i:s', filemtime($path)),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return round($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
