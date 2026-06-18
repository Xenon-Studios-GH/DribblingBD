<?php

namespace App\Http\Controllers\Tracking;

use App\Http\Controllers\Controller;
use App\Services\Tracking\CapiService;
use Illuminate\Http\Request;

class CapiBridgeController extends Controller
{
    public function __invoke(Request $request, CapiService $capi)
    {
        $validated = $request->validate([
            'event' => 'required|string|max:50',
            'data' => 'nullable|array',
        ]);

        $capi->processClientEvent($validated);

        return response()->noContent();
    }
}
