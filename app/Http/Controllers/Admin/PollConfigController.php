<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PollConfigController extends Controller
{
    public function index()
    {
        $configs = DB::table('tracker')->get()->keyBy('key');
        return response()->json($configs);
    }

    public function sync(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:100',
            'type' => 'nullable|string|in:poll,task',
            'name' => 'nullable|string|max:200',
            'is_active' => 'required|boolean',
            'interval_ms' => 'nullable|integer|min:5000|max:300000',
            'last_run_at' => 'nullable|integer',
            'run_count' => 'nullable|integer',
        ]);

        $key = $validated['key'];
        $existing = DB::table('tracker')->where('key', $key)->first();

        $update = [
            'type' => $validated['type'] ?? 'poll',
            'is_active' => $validated['is_active'],
            'updated_at' => now(),
        ];

        if (isset($validated['interval_ms'])) {
            $update['interval_ms'] = $validated['interval_ms'];
        }

        if (isset($validated['name'])) {
            $update['name'] = $validated['name'];
        }

        if ($existing) {
            if (isset($validated['last_run_at'])) {
                $update['last_run_at'] = now();
                $update['run_count'] = DB::raw('run_count + 1');
            }
            DB::table('tracker')->where('key', $key)->update($update);
        } else {
            $insert = array_merge($update, [
                'key' => $key,
                'name' => $validated['name'] ?? str_replace(['-', '_'], ' ', $key),
                'created_at' => now(),
            ]);
            if (isset($validated['last_run_at'])) {
                $insert['last_run_at'] = now();
                $insert['run_count'] = 1;
            }
            DB::table('tracker')->insert($insert);
        }

        return response()->noContent();
    }
}
