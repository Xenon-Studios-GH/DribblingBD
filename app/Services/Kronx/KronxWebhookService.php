<?php

namespace App\Services\Kronx;

use App\Models\Inquiry;
use App\Models\KronxWebhookDelivery;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class KronxWebhookService
{
    public function verifySignature(string $rawBody, string $signatureHeader, string $secret): bool
    {
        $prefix = 'sha256=';
        if (!str_starts_with($signatureHeader, $prefix)) {
            return false;
        }

        $expected = $prefix . hash_hmac('sha256', $rawBody, $secret);
        return hash_equals($expected, $signatureHeader);
    }

    public function processEvent(array $payload, string $deliveryUuid): KronxWebhookDelivery
    {
        $event = $payload['event'] ?? 'unknown';
        $data = $payload['data'] ?? [];

        $delivery = KronxWebhookDelivery::create([
            'delivery_uuid' => $deliveryUuid,
            'event' => $event,
            'payload' => $payload,
        ]);

        try {
            match ($event) {
                'order.confirmed' => $this->processOrderConfirmed($data),
                'lead.captured' => $this->processLeadCaptured($data),
                'webhook.test' => null,
                default => Log::channel('kronx')->warning('Unknown webhook event', ['event' => $event]),
            };

            $delivery->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::channel('kronx')->error('Webhook processing failed', [
                'event' => $event,
                'delivery_uuid' => $deliveryUuid,
                'error' => $e->getMessage(),
            ]);

            $delivery->update([
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
        }

        return $delivery->fresh();
    }

    private function processOrderConfirmed(array $data): Order
    {
        $orderData = $data['order_data'] ?? [];
        $products = $data['products'] ?? [];

        $order = Order::create([
            'order_no' => Order::generateOrderNo(),
            'customer_name' => $data['customer_name'] ?? ($orderData['customer_name_m'] ?? 'Unknown'),
            'phone' => $data['customer_contact'] ?? ($orderData['customer_phone_m'] ?? ''),
            'address' => $data['delivery_address'] ?? ($orderData['delivery_address_m'] ?? ''),
            'products' => $products,
            'total_amount' => $data['total_amount'] ?? 0,
            'advanced_payment' => 0,
            'pending_payment' => $data['total_amount'] ?? 0,
            'payment_method' => 'Kronx',
            'status' => 'pending',
            'created_by' => null,
            'notes' => json_encode([
                'kronx_order_id' => $data['order_id'] ?? null,
                'order_data' => $orderData,
            ]),
        ]);

        Log::channel('kronx')->info('Order created from webhook', [
            'order_no' => $order->order_no,
            'kronx_order_id' => $data['order_id'] ?? null,
        ]);

        return $order;
    }

    private function processLeadCaptured(array $data): Inquiry
    {
        $contact = $data['contact'] ?? [];

        $inquiry = Inquiry::create([
            'name' => $contact['name'] ?? ($data['customer_name'] ?? 'Unknown'),
            'phone' => $contact['phone'] ?? ($data['customer_contact'] ?? ''),
            'details' => json_encode(['source' => 'kronx', 'data' => $data]),
            'is_read' => false,
        ]);

        Log::channel('kronx')->info('Lead captured from webhook', [
            'inquiry_id' => $inquiry->id,
        ]);

        return $inquiry;
    }
}
