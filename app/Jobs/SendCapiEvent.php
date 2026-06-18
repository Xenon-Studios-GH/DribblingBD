<?php

namespace App\Jobs;

use App\Models\TrackingEventLog;
use App\Services\Tracking\CapiService;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendCapiEvent implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(
        public int $pixelId,
        public string $eventName,
        public array $data = []
    ) {
        $this->onQueue('tracking');
    }

    public function uniqueId(): string
    {
        $eventId = $this->data['event_id'] ?? '';
        return "capi_{$this->pixelId}_{$this->eventName}_{$eventId}";
    }

    public function handle(CapiService $capi): void
    {
        $result = $capi->send($this->pixelId, $this->eventName, $this->data);

        $log = TrackingEventLog::where('pixel_id', $this->pixelId)
            ->where('event_name', $this->eventName)
            ->where('status', 'queued')
            ->latest()
            ->first();

        $log?->update([
            'status' => $result['status'] ?? 'failed',
            'response' => $result['response'] ?? [],
            'sent_at' => now(),
        ]);

        $status = $result['status'] ?? 'failed';
        $description = "CAPI event '{$this->eventName}' " . ($status === 'sent' ? 'sent' : 'failed') . " (pixel #{$this->pixelId})";

        app(\App\Services\WorkLogService::class)->log(
            action: 'Tracking Event',
            module: 'seo',
            referenceId: $log?->id,
            description: $description,
        );

        if (($result['success'] ?? false) === false) {
            $this->fail(new \RuntimeException(
                $result['response']['error']['message']
                ?? 'CAPI request failed'
            ));
        }
    }
}
