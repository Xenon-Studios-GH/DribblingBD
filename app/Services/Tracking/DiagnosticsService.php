<?php

namespace App\Services\Tracking;

use App\Models\TrackingEventLog;
use App\Models\TrackingPixel;
use App\Models\TrackingSetting;

class DiagnosticsService
{
    public function validatePixelId(string $platform, string $id): array
    {
        $patterns = [
            'meta' => '/^\d{15,20}$/',
            'ga4' => '/^G-[A-Z0-9]{5,}$/',
            'gtm' => '/^GTM-[A-Z0-9]{5,}$/',
            'google_ads' => '/^AW-\d{5,}$/',
            'clarity' => '/^[a-zA-Z0-9]{10,}$/',
        ];

        $pattern = $patterns[$platform] ?? null;
        if (!$pattern) {
            return ['valid' => false, 'message' => "Unknown platform: {$platform}"];
        }

        $valid = (bool) preg_match($pattern, $id);
        return [
            'valid' => $valid,
            'message' => $valid ? 'Valid format' : "Invalid format for {$platform}. Expected: {$pattern}",
        ];
    }

    public function fireTestEvent(int $pixelId): array
    {
        $pixel = TrackingPixel::findOrFail($pixelId);
        if ($pixel->platform !== 'meta') {
            return ['success' => false, 'message' => 'Test events only supported for Meta Pixel'];
        }

        $data = [
            'event_name' => 'TestEvent',
            'event_time' => time(),
            'action_source' => 'website',
            'event_id' => 'test_' . uniqid(),
            'test_event_code' => 'TEST' . random_int(10000, 99999),
        ];

        $options = $pixel->options ?? [];
        $token = $options['capi_token'] ?? null;
        if (!$token) {
            return ['success' => false, 'message' => 'No CAPI token configured'];
        }

        $endpoint = TrackingSetting::getValue('capi_endpoint', 'https://graph.facebook.com/v18.0');
        $payload = ['data' => [$data], 'access_token' => $token, 'test_event_code' => $data['test_event_code']];

        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)
                ->post("{$endpoint}/{$pixel->pixel_id}/events", $payload);

            return [
                'success' => $response->successful(),
                'response' => $response->json(),
                'test_code' => $data['test_event_code'],
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function checkHealth(): array
    {
        $pixels = TrackingPixel::active()->get();
        $statuses = [];

        foreach ($pixels as $pixel) {
            $lastEvent = $pixel->eventLogs()->where('status', 'sent')
                ->latest('sent_at')->first();

            $statuses[] = [
                'id' => $pixel->id,
                'name' => $pixel->name,
                'platform' => $pixel->platform,
                'platform_label' => $pixel->platform_label,
                'is_active' => $pixel->is_active,
                'has_capi' => !empty(($pixel->options ?? [])['capi_token']),
                'last_event_sent' => $lastEvent?->sent_at?->diffForHumans() ?? 'Never',
                'total_events' => $pixel->eventLogs()->count(),
                'failed_events' => $pixel->eventLogs()->where('status', 'failed')->count(),
            ];
        }

        $debugMode = filter_var(TrackingSetting::getValue('debug_mode', 'false'), FILTER_VALIDATE_BOOLEAN);
        $capiEnabled = filter_var(TrackingSetting::getValue('capi_enabled', 'false'), FILTER_VALIDATE_BOOLEAN);

        return [
            'total_pixels' => $pixels->count(),
            'active_pixels' => $pixels->count(),
            'debug_mode' => $debugMode,
            'capi_enabled' => $capiEnabled,
            'pixels' => $statuses,
        ];
    }

    public function toggleDebugMode(bool $enabled): void
    {
        TrackingSetting::setValue('debug_mode', $enabled ? 'true' : 'false', 'boolean');
    }

    public function isDebugMode(): bool
    {
        return filter_var(TrackingSetting::getValue('debug_mode', 'false'), FILTER_VALIDATE_BOOLEAN);
    }
}
