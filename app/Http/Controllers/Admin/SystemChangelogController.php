<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SystemChangelogController extends Controller
{
    public function index(Request $request)
    {
        $query = DB::table('system_changelog');

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('version', 'like', "%{$s}%");
            });
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        $changelogs = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('monitoring.changelog', compact('changelogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:20',
            'category' => 'required|in:security,bugfix,feature,refactor',
            'severity' => 'required|in:critical,high,medium,low',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'files_affected' => 'nullable|string',
            'before_state' => 'nullable|string',
            'after_state' => 'nullable|string',
        ]);

        $files = array_filter(array_map('trim', explode(',', $validated['files_affected'] ?? '')));

        DB::table('system_changelog')->insert([
            'version' => $validated['version'],
            'category' => $validated['category'],
            'severity' => $validated['severity'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'files_affected' => json_encode($files),
            'before_state' => $validated['before_state'] ?? null,
            'after_state' => $validated['after_state'] ?? null,
            'author' => auth()->user()->name ?? 'system',
            'applied_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Changelog entry created.');
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:20',
            'category' => 'required|in:security,bugfix,feature,refactor',
            'severity' => 'required|in:critical,high,medium,low',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'files_affected' => 'nullable|string',
            'before_state' => 'nullable|string',
            'after_state' => 'nullable|string',
        ]);

        $files = array_filter(array_map('trim', explode(',', $validated['files_affected'] ?? '')));

        DB::table('system_changelog')->where('id', $id)->update([
            'version' => $validated['version'],
            'category' => $validated['category'],
            'severity' => $validated['severity'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'files_affected' => json_encode($files),
            'before_state' => $validated['before_state'] ?? null,
            'after_state' => $validated['after_state'] ?? null,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Changelog entry updated.');
    }

    public function destroy(int $id)
    {
        DB::table('system_changelog')->where('id', $id)->delete();
        return back()->with('success', 'Changelog entry deleted.');
    }
}
