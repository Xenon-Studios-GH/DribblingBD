<?php

namespace App\Services;

use App\Models\WorkLog;
use Illuminate\Support\Facades\Auth;

class WorkLogService
{
    public function log(string $action, string $module, ?int $referenceId = null, ?string $description = null): WorkLog
    {
        return WorkLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'module' => $module,
            'reference_id' => $referenceId,
            'description' => $description,
        ]);
    }

    public function getLogs(array $filters = [], int $perPage = 20)
    {
        $query = WorkLog::with('user')->latest();

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['module'])) {
            $query->where('module', $filters['module']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', 'like', '%' . $filters['action'] . '%');
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->paginate($perPage);
    }
}
