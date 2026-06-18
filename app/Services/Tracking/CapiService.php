<?php

namespace App\Services\Tracking;

use App\Jobs\SendCapiEvent;
use App\Models\TrackingEventLog;
use App\Models\TrackingPixel;
use App\Models\TrackingSetting;
use Illuminate\Support\Facades\Http;

class CapiService
{
    public function dispatch(string $eventName, array $data = [], ?int $pixelId = null): void
    {
        $pixels = $pixelId
            ? TrackingPixel::where('id', $pixelId)->active()->get()
            : TrackingPixel::active()->byPlatform('meta')->get();

        foreach ($pixels as $pixel) {
            $options = $pixel->options ?? [];
            if (empty($options['capi_token'])) continue;

            $pixel->eventLogs()->create([
                'event_name' => $eventName,
                'event_data' => $data,
                'status' => 'queued',
            ]);

            SendCapiEvent::dispatch($pixel->id, $eventName, $data)
                ->onQueue('tracking');
        }
    }

    public function send(int $pixelId, string $eventName, array $data): array
    {
        $pixel = TrackingPixel::findOrFail($pixelId);
        $options = $pixel->options ?? [];
        $token = $options['capi_token'] ?? null;
        $endpoint = TrackingSetting::getValue('capi_endpoint', 'https://graph.facebook.com/v18.0');

        if (!$token) {
            return ['error' => 'No CAPI token configured for pixel'];
        }

        $eventData = array_merge($data, [
            'event_name' => $eventName,
            'event_time' => time(),
            'action_source' => 'website',
        ]);

        if (!isset($data['event_id'])) {
            $eventData['event_id'] = $pixel->id . '_' . time() . '_' . uniqid();
        }

        $payload = [
            'data' => [$eventData],
            'access_token' => $token,
        ];

        try {
            $response = Http::timeout(5)
                ->post("{$endpoint}/{$pixel->pixel_id}/events", $payload);

            $body = $response->json();
            $isSuccess = $response->successful();

            return [
                'success' => $isSuccess,
                'status' => $isSuccess ? 'sent' : 'failed',
                'response' => $body,
                'events_received' => $body['events_received'] ?? 0,
                'messages' => $body['messages'] ?? [],
                'fbtrace_id' => $body['__trace_id'] ?? null,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'status' => 'failed',
                'response' => ['error' => $e->getMessage()],
                'events_received' => 0,
            ];
        }
    }

    public function retry(int $logId): void
    {
        $log = TrackingEventLog::findOrFail($logId);
        if (!$log->pixel_id) return;

        SendCapiEvent::dispatch($log->pixel_id, $log->event_name, $log->event_data ?? [])
            ->onQueue('tracking');

        $log->update(['status' => 'queued']);
    }

    public function processClientEvent(array $payload): void
    {
        $eventName = $payload['event'] ?? 'Unknown';
        $data = $payload['data'] ?? [];

        $this->dispatch($eventName, $data);
    }
}
