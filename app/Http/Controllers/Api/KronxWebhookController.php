<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KronxWebhookDelivery;
use App\Services\Kronx\KronxWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KronxWebhookController extends Controller
{
    public function __construct(
        private readonly KronxWebhookService $webhookService,
    ) {}

    public function __invoke(Request $request)
    {
        $rawBody = $request->getContent();
        $signature = $request->header('X-Kronx-Signature', '');
        $deliveryUuid = $request->header('X-Kronx-Delivery', '');

        // 1. Verify signature
        $secret = config('kronx.webhook_secret');
        if (!$secret || !$this->webhookService->verifySignature($rawBody, $signature, $secret)) {
            Log::channel('kronx')->warning('Webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 200);
        }

        if (empty($deliveryUuid)) {
            Log::channel('kronx')->warning('Webhook missing X-Kronx-Delivery header');
            return response()->json(['error' => 'Missing delivery UUID'], 200);
        }

        // 2. Dedup
        if (KronxWebhookDelivery::where('delivery_uuid', $deliveryUuid)->exists()) {
            return response()->json(['status' => 'duplicate'], 200);
        }

        // 3. Process
        $payload = json_decode($rawBody, true);
        $this->webhookService->processEvent($payload, $deliveryUuid);

        return response()->json(['status' => 'ok'], 200);
    }
}
