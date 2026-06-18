<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoRedirect;
use App\Services\WorkLogService;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    protected WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function index(Request $request)
    {
        $query = SeoRedirect::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('from_url', 'like', "%{$search}%")
                  ->orWhere('to_url', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('match_type')) {
            $query->where('match_type', $request->match_type);
        }

        $redirects = $query->latest()->paginate(20);

        if ($request->ajax()) {
            return view('seo.redirects._table', compact('redirects'));
        }

        return view('seo.redirects.index', compact('redirects'));
    }

    public function create()
    {
        return view('seo.redirects.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_url' => 'required|string|max:500|starts_with:/|unique:seo_redirects,from_url',
            'to_url' => 'required|string|max:500',
            'status_code' => 'required|in:301,302',
            'match_type' => 'required|in:exact,prefix,regex',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $redirect = SeoRedirect::create($validated);

        cache()->forget('seo.redirects.active');

        $this->workLogService->log('Redirect Created', 'seo', $redirect->id, "301 redirect: {$redirect->from_url} → {$redirect->to_url}");

        return redirect(admin_route('seo.redirects.index'))
            ->with('success', 'Redirect created successfully.');
    }

    public function edit(SeoRedirect $redirect)
    {
        return view('seo.redirects.form', compact('redirect'));
    }

    public function update(Request $request, SeoRedirect $redirect)
    {
        $validated = $request->validate([
            'from_url' => 'required|string|max:500|starts_with:/|unique:seo_redirects,from_url,' . $redirect->id,
            'to_url' => 'required|string|max:500',
            'status_code' => 'required|in:301,302',
            'match_type' => 'required|in:exact,prefix,regex',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $redirect->update($validated);

        cache()->forget('seo.redirects.active');

        $this->workLogService->log('Redirect Updated', 'seo', $redirect->id, "Redirect updated: {$redirect->from_url} → {$redirect->to_url}");

        return redirect(admin_route('seo.redirects.index'))
            ->with('success', 'Redirect updated successfully.');
    }

    public function destroy(SeoRedirect $redirect)
    {
        $redirect->delete();

        cache()->forget('seo.redirects.active');

        $this->workLogService->log('Redirect Deleted', 'seo', $redirect->id, "Redirect deleted: {$redirect->from_url}");

        return redirect(admin_route('seo.redirects.index'))
            ->with('success', 'Redirect deleted.');
    }
}
