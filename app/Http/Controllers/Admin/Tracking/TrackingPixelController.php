<?php

namespace App\Http\Controllers\Admin\Tracking;

use App\Http\Controllers\Controller;
use App\Models\TrackingPixel;
use App\Services\Tracking\PixelRendererService;
use Illuminate\Http\Request;

class TrackingPixelController extends Controller
{
    public function index(PixelRendererService $renderer)
    {
        $pixels = TrackingPixel::ordered()->get();
        return view('tracking.index', compact('pixels'));
    }

    public function create()
    {
        $platforms = [
            'meta' => 'Meta Pixel',
            'ga4' => 'Google Analytics 4',
            'gtm' => 'Google Tag Manager',
            'google_ads' => 'Google Ads',
            'clarity' => 'Microsoft Clarity',
        ];
        return view('tracking.form', compact('platforms'));
    }

    public function store(Request $request, PixelRendererService $renderer)
    {
        $validated = $request->validate([
            'platform' => 'required|in:meta,ga4,gtm,google_ads,clarity',
            'name' => 'required|string|max:100',
            'pixel_id' => 'required|string|max:255',
            'is_active' => 'boolean',
            'load_position' => 'required|in:head,body',
            'sort_order' => 'integer|min:0',
            'options.capi_token' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        TrackingPixel::create($validated);
        $renderer->clearCache();

        return redirect()->route('tracking.index', ['role' => $request->route('role')])
            ->with('success', 'Pixel created successfully');
    }

    public function edit(TrackingPixel $trackingPixel)
    {
        $pixel = $trackingPixel;
        $platforms = [
            'meta' => 'Meta Pixel',
            'ga4' => 'Google Analytics 4',
            'gtm' => 'Google Tag Manager',
            'google_ads' => 'Google Ads',
            'clarity' => 'Microsoft Clarity',
        ];
        return view('tracking.form', compact('pixel', 'platforms'));
    }

    public function update(Request $request, TrackingPixel $trackingPixel, PixelRendererService $renderer)
    {
        $validated = $request->validate([
            'platform' => 'required|in:meta,ga4,gtm,google_ads,clarity',
            'name' => 'required|string|max:100',
            'pixel_id' => 'required|string|max:255',
            'is_active' => 'boolean',
            'load_position' => 'required|in:head,body',
            'sort_order' => 'integer|min:0',
            'options.capi_token' => 'nullable|string|max:255',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $trackingPixel->update($validated);
        $renderer->clearCache();

        return redirect()->route('tracking.index', ['role' => $request->route('role')])
            ->with('success', 'Pixel updated successfully');
    }

    public function destroy(TrackingPixel $trackingPixel, PixelRendererService $renderer)
    {
        $trackingPixel->delete();
        $renderer->clearCache();

        return redirect()->route('tracking.index', ['role' => request()->route('role')])
            ->with('success', 'Pixel deleted successfully');
    }

    public function toggle(TrackingPixel $trackingPixel, PixelRendererService $renderer)
    {
        $trackingPixel->update(['is_active' => !$trackingPixel->is_active]);
        $renderer->clearCache();

        return redirect()->route('tracking.index', ['role' => request()->route('role')])
            ->with('success', $trackingPixel->is_active ? 'Pixel enabled' : 'Pixel disabled');
    }
}
