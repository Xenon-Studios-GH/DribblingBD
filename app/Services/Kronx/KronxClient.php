<?php

namespace App\Services\Kronx;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KronxClient
{
    private int $timeout = 15;

    public function __construct(
        private readonly string $baseUrl,
        private readonly string $apiKey,
    ) {}

    public function me(): array
    {
        return $this->send('GET', '/me');
    }

    public function createProduct(array $data): array
    {
        return $this->send('POST', '/products', $data);
    }

    public function updateProduct(string $kronxId, array $data): array
    {
        return $this->send('PUT', "/products/{$kronxId}", $data);
    }

    public function bulkUpdateStock(array $items): array
    {
        return $this->send('POST', '/products/bulk-stock', ['items' => $items]);
    }

    private function send(string $method, string $path, array $body = []): array
    {
        $url = rtrim($this->baseUrl, '/') . $path;

        $response = Http::withToken($this->apiKey)
            ->timeout($this->timeout)
            ->retry(3, 100, function (bool $decided, \Illuminate\Http\Client\Response $response) {
                return $decided && $response->serverError();
            })
            ->send($method, $url, $body ? ['json' => $body] : []);

        Log::channel('kronx')->info('Kronx API call', [
            'method' => $method,
            'path' => $path,
            'status' => $response->status(),
        ]);

        if ($response->failed()) {
            Log::channel('kronx')->error('Kronx API error', [
                'method' => $method,
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->status() === 401 || $response->status() === 403) {
                Log::channel('kronx')->critical('Kronx API key rejected — skipping cycle');
                throw new \RuntimeException('Kronx API authentication failed (401/403)');
            }

            $response->throw();
        }

        return $response->json() ?? [];
    }
}
