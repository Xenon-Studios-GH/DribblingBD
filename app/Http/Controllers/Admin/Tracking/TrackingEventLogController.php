<?php

namespace App\Http\Controllers\Admin\Tracking;

use App\Http\Controllers\Controller;
use App\Models\TrackingEventLog;
use App\Services\Tracking\CapiService;

class TrackingEventLogController extends Controller
{
    public function index()
    {
        $events = TrackingEventLog::with('pixel')
            ->latest()
            ->paginate(50);

        return view('tracking.events', compact('events'));
    }

    public function retry(TrackingEventLog $trackingEventLog, CapiService $capi)
    {
        $capi->retry($trackingEventLog->id);

        return redirect()->route('tracking.events', ['role' => request()->route('role')])
            ->with('success', 'Event re-queued for retry');
    }
}
