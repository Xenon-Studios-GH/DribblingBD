<?php

namespace App\Console\Commands;

use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\Order;
use App\Models\PendingOrderTransaction;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\WorkLogService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditSystemConsistency extends Command
{
    protected $signature = 'app:audit-consistency {--auto-fix : Apply auto-fixes for known issues}';
    protected $description = 'Global brain audit — checks order, finance, stock consistency every 5 min';

    private array $issues = [];
    private int $fixedCount = 0;

    public function handle(WorkLogService $workLogService): int
    {
        $this->info('=== Global Audit Starting ===');
        $start = microtime(true);

        $this->checkOrderPaymentIntegrity();
        $this->checkPendingTransactions();
        $this->checkFinanceTransactions();
        $this->checkStockIntegrity();
        $this->checkDataConsistency();

        $elapsed = round(microtime(true) - $start, 2);
        $totalIssues = count($this->issues);

        $this->newLine();
        $this->info("──────────────────────────────────────");
        $this->info("Audit completed in {$elapsed}s");
        $this->info("Issues found: {$totalIssues} | Auto-fixed: {$this->fixedCount}");

        if ($totalIssues > 0) {
            $lines = [];
            foreach ($this->issues as $i) {
                $icon = $i['fixed'] ? "\xE2\x9C\x85" : "\xE2\x9D\x8C";
                $this->warn("  {$icon} [{$i['check']}] {$i['message']}" . ($i['fixed'] ? ' (fixed)' : ''));
                $lines[] = "[{$i['check']}] {$i['message']}" . ($i['fixed'] ? ' (fixed)' : '');
            }

            $summary = "Audit: {$totalIssues} issue(s), {$this->fixedCount} auto-fixed";
            $workLogService->log(
                action: $summary,
                module: 'Audit',
                description: implode("\n", $lines),
                userId: User::where('role', 'superadmin')->value('id'),
            );
        }

        $this->info('=== Audit Complete ===');

        return self::SUCCESS;
    }

    private function addIssue(string $check, string $message, bool $fixed = false): void
    {
        $this->issues[] = ['check' => $check, 'message' => $message, 'fixed' => $fixed];
        if ($fixed) {
            $this->fixedCount++;
        }
    }

    private function shouldFix(): bool
    {
        return $this->option('auto-fix');
    }

    // ─── A. Order Payment Integrity ───────────────────────────────

    private function checkOrderPaymentIntegrity(): void
    {
        $this->info("\n[A] Order Payment Integrity...");

        Order::withTrashed()->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $expectedPending = max(0, (float) $order->total_amount - (float) $order->advanced_payment);

                if (abs((float) $order->pending_payment - $expectedPending) > 0.01) {
                    if ($this->shouldFix()) {
                        Order::withTrashed()->where('id', $order->id)->update(['pending_payment' => $expectedPending]);
                        $this->addIssue('A1', "Order #{$order->order_no}: pending_payment {$order->pending_payment} corrected to {$expectedPending}", true);
                    } else {
                        $this->addIssue('A1', "Order #{$order->order_no}: pending_payment {$order->pending_payment} should be {$expectedPending}");
                    }
                }

                if ((float) $order->pending_payment < 0) {
                    $this->addIssue('A2', "Order #{$order->order_no}: negative pending_payment ({$order->pending_payment})");
                }
                if ((float) $order->advanced_payment < 0) {
                    $this->addIssue('A2', "Order #{$order->order_no}: negative advanced_payment ({$order->advanced_payment})");
                }

                if (in_array($order->status, ['refund', 'return']) && (float) $order->pending_payment > 0) {
                    if ($this->shouldFix()) {
                        Order::withTrashed()->where('id', $order->id)->update(['pending_payment' => 0]);
                        $this->addIssue('A3', "Order #{$order->order_no} ({$order->status}): pending_payment {$order->pending_payment} zeroed", true);
                    } else {
                        $this->addIssue('A3', "Order #{$order->order_no} ({$order->status}): pending_payment should be 0 (currently {$order->pending_payment})");
                    }
                }
            }
        });

        Order::where('status', 'delivered')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                $exists = PendingOrderTransaction::where('order_id', $order->id)->exists();
                if (!$exists) {
                    if ($this->shouldFix()) {
                        $this->createMissingPendingTransaction($order);
                        $this->addIssue('A4', "Order #{$order->order_no} (delivered): missing PendingOrderTransaction created", true);
                    } else {
                        $this->addIssue('A4', "Order #{$order->order_no} (delivered): missing PendingOrderTransaction");
                    }
                }
            }
        });
    }

    private function createMissingPendingTransaction(Order $order): void
    {
        $deliveryCharge = (float) ($order->delivery_charge ?? 0);
        $products = $order->products ?? [];
        $dtfSales = 0;
        $patchSales = 0;
        $patchPrice = (float) ($order->patch_price ?? 0);
        foreach ($products as $p) {
            if (!empty($p['dtf']) || !empty($p['dtf_name']) || !empty($p['dtf_number'])) {
                $dtfSales += 200;
            }
            if (!empty($p['patch'])) {
                $patchSales += $patchPrice * 2;
            }
        }
        $productSales = (float) $order->total_amount - $deliveryCharge - $dtfSales - $patchSales;

        PendingOrderTransaction::create([
            'order_id' => $order->id,
            'order_no' => $order->order_no,
            'customer_name' => $order->customer_name,
            'total_amount' => $order->total_amount,
            'delivery_charge' => $deliveryCharge,
            'product_sales_amount' => max(0, $productSales),
            'dtf_sales_amount' => $dtfSales,
            'patch_sales_amount' => $patchSales,
        ]);
    }

    // ─── B. PendingOrderTransaction Integrity ─────────────────────

    private function checkPendingTransactions(): void
    {
        $this->info('[B] PendingOrderTransaction Integrity...');

        PendingOrderTransaction::chunk(100, function ($pendingTx) {
            foreach ($pendingTx as $pt) {
                $order = Order::withTrashed()->find($pt->order_id);
                if (!$order) {
                    $this->addIssue('B1', "PendingOrderTransaction #{$pt->id}: order_id {$pt->order_id} not found");
                    continue;
                }
                if (abs((float) $pt->total_amount - (float) $order->total_amount) > 0.01) {
                    $this->addIssue('B3', "PendingOrderTransaction #{$pt->id} (order {$pt->order_no}): amount mismatch — PT: {$pt->total_amount}, Order: {$order->total_amount}");
                }
            }
        });

        $confirmed = PendingOrderTransaction::where('status', 'confirmed')->get();
        foreach ($confirmed as $pt) {
            $matches = FinanceTransaction::where('order_id', $pt->order_id)->count();
            if ($matches === 0) {
                $this->addIssue('B2', "PendingOrderTransaction #{$pt->id} (order {$pt->order_no}) confirmed but no FinanceTransaction found");
            }
        }
    }

    // ─── C. Finance Transaction Integrity ─────────────────────────

    private function checkFinanceTransactions(): void
    {
        $this->info('[C] Finance Transaction Integrity...');

        FinanceTransaction::chunk(100, function ($txns) {
            foreach ($txns as $txn) {
                if ((float) $txn->amount < 0) {
                    $this->addIssue('C1', "FinanceTransaction #{$txn->id}: negative amount {$txn->amount}");
                }
                if ($txn->category_id && !FinanceCategory::find($txn->category_id)) {
                    $this->addIssue('C2', "FinanceTransaction #{$txn->id}: invalid category_id {$txn->category_id}");
                }
            }
        });
    }

    // ─── D. Stock Integrity ──────────────────────────────────────

    private function checkStockIntegrity(): void
    {
        $this->info('[D] Stock Integrity...');

        Stock::chunk(100, function ($stocks) {
            foreach ($stocks as $stock) {
                if ($stock->quantity < 0) {
                    if ($this->shouldFix()) {
                        $stock->update(['quantity' => 0]);
                        $this->addIssue('D1', "Stock #{$stock->id} (product {$stock->product_id}, size {$stock->size}): negative qty {$stock->quantity} clamped to 0", true);
                    } else {
                        $this->addIssue('D1', "Stock #{$stock->id} (product {$stock->product_id}, size {$stock->size}): negative qty {$stock->quantity}");
                    }
                }
            }
        });

        $badTxns = StockTransaction::whereRaw('
            (type = "in" AND stock_after != stock_before + quantity)
            OR (type = "out" AND stock_after != stock_before - quantity)
        ')->limit(50)->get();

        foreach ($badTxns as $txn) {
            $this->addIssue('D2', "StockTransaction #{$txn->id} (product {$txn->product_id}, {$txn->type}): before={$txn->stock_before}, qty={$txn->quantity}, after={$txn->stock_after} — reconciliation mismatch");
        }
    }

    // ─── E. General Data Consistency ─────────────────────────────

    private function checkDataConsistency(): void
    {
        $this->info('[E] General Data Consistency...');

        Order::whereNotNull('created_by')->chunk(100, function ($orders) {
            foreach ($orders as $order) {
                if (!User::find($order->created_by)) {
                    $this->addIssue('E1', "Order #{$order->order_no}: invalid created_by {$order->created_by}");
                }
            }
        });

        $duplicates = Order::withTrashed()
            ->select('order_no', DB::raw('COUNT(*) as cnt'))
            ->groupBy('order_no')
            ->having('cnt', '>', 1)
            ->get();

        foreach ($duplicates as $dup) {
            $this->addIssue('E2', "Duplicate order_no: '{$dup->order_no}' appears {$dup->cnt} times");
        }
    }
}
