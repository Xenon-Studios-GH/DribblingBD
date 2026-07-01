<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KronxWebhookDelivery;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class KronxLogController extends Controller
{
    public function index()
    {
        $deliveries = KronxWebhookDelivery::latest()->paginate(25);

        $logEntries = $this->parseLogFile(storage_path('logs/kronx.log'), 50);

        $stats = [
            'synced_products' => Product::whereNotNull('kronx_product_id')->count(),
            'pending_products' => Product::whereNull('kronx_product_id')->count(),
            'total_webhooks' => KronxWebhookDelivery::count(),
        ];

        return view('kronx.index', compact('deliveries', 'logEntries', 'stats'));
    }

    public function syncProducts()
    {
        Artisan::call('kronx:sync-products');
        return back()->with('success', Artisan::output());
    }

    public function syncStock()
    {
        Artisan::call('kronx:sync-stock');
        return back()->with('success', Artisan::output());
    }

    private function parseLogFile(string $path, int $lines): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $file = new \SplFileObject($path, 'r');
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();

        $startLine = max(0, $totalLines - $lines);
        $entries = [];

        for ($i = $startLine; $i < $totalLines; $i++) {
            $file->seek($i);
            $line = trim($file->current());
            if (empty($line)) continue;

            if (preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)/', $line, $m)) {
                $entries[] = [
                    'timestamp' => $m[1],
                    'channel' => $m[2],
                    'level' => $m[3],
                    'message' => $m[4],
                ];
            } else {
                $entries[] = [
                    'timestamp' => '',
                    'channel' => '',
                    'level' => 'INFO',
                    'message' => $line,
                ];
            }
        }

        return array_reverse($entries);
    }
}
