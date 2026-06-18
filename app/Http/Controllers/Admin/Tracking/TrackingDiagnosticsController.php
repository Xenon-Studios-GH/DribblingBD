<?php

namespace App\Http\Controllers\Admin\Tracking;

use App\Http\Controllers\Controller;
use App\Models\TrackingPixel;
use App\Services\Tracking\DiagnosticsService;
use Illuminate\Http\Request;

class TrackingDiagnosticsController extends Controller
{
    public function index(DiagnosticsService $diag)
    {
        $health = $diag->checkHealth();
        return view('tracking.diagnostics', compact('health'));
    }

    public function testEvent(Request $request, TrackingPixel $trackingPixel, DiagnosticsService $diag)
    {
        $result = $diag->fireTestEvent($trackingPixel->id);

        if ($result['success'] ?? false) {
            return redirect()->route('tracking.diagnostics', ['role' => $request->route('role')])
                ->with('success', "Test event sent! Check Meta Events Manager with test code: {$result['test_code']}");
        }

        return redirect()->route('tracking.diagnostics', ['role' => $request->route('role')])
            ->with('error', 'Test event failed: ' . ($result['message'] ?? $result['error'] ?? 'Unknown error'));
    }

    public function toggleDebug(Request $request, DiagnosticsService $diag)
    {
        $enabled = $request->boolean('enabled', false);
        $diag->toggleDebugMode($enabled);

        return redirect()->route('tracking.diagnostics', ['role' => $request->route('role')])
            ->with('success', $enabled ? 'Debug mode enabled' : 'Debug mode disabled');
    }
}
