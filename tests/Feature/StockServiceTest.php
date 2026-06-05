<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransaction;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockService $stockService;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stockService = $this->app->make(StockService::class);
        $this->product = Product::factory()->create();
        $user = User::factory()->create();
        Auth::login($user);
    }

    public function test_preview_in_returns_correct_projections()
    {
        Stock::factory()->create(['product_id' => $this->product->id, 'size' => 'M', 'quantity' => 10]);

        $preview = $this->stockService->previewIn($this->product, 'M', 5);

        $this->assertEquals(10, $preview['current_stock']);
        $this->assertEquals(5, $preview['change']);
        $this->assertEquals(15, $preview['new_stock']);
    }

    public function test_preview_in_creates_stock_if_not_exists()
    {
        $preview = $this->stockService->previewIn($this->product, 'XL', 5);

        $this->assertEquals(0, $preview['current_stock']);
        $this->assertEquals(5, $preview['new_stock']);
    }

    public function test_preview_out_throws_on_insufficient_stock()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stockService->previewOut($this->product, 'M', 1);
    }

    public function test_preview_out_returns_correctly_with_sufficient_stock()
    {
        Stock::factory()->create(['product_id' => $this->product->id, 'size' => 'M', 'quantity' => 10]);

        $preview = $this->stockService->previewOut($this->product, 'M', 3);

        $this->assertEquals(10, $preview['current_stock']);
        $this->assertEquals(-3, $preview['change']);
        $this->assertEquals(7, $preview['new_stock']);
    }

    public function test_stock_in_increases_quantity()
    {
        $stock = $this->stockService->stockIn($this->product, 'L', 10);

        $this->assertEquals(10, $stock->quantity);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'stock_before' => 0,
            'stock_after' => 10,
        ]);
    }

    public function test_stock_in_accumulates()
    {
        $this->stockService->stockIn($this->product, 'L', 5);
        $stock = $this->stockService->stockIn($this->product, 'L', 3);

        $this->assertEquals(8, $stock->quantity);
    }

    public function test_stock_out_decreases_quantity()
    {
        Stock::factory()->create(['product_id' => $this->product->id, 'size' => 'M', 'quantity' => 20]);

        $stock = $this->stockService->stockOut($this->product, 'M', 5);

        $this->assertEquals(15, $stock->quantity);
        $this->assertDatabaseHas('stock_transactions', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 5,
            'stock_before' => 20,
            'stock_after' => 15,
        ]);
    }

    public function test_stock_out_throws_on_insufficient_stock()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stockService->stockOut($this->product, 'M', 1);
    }

    public function test_get_or_create_stock_creates_new()
    {
        $stock = $this->stockService->getOrCreateStock($this->product, 'XXL');

        $this->assertDatabaseHas('stocks', [
            'product_id' => $this->product->id,
            'size' => 'XXL',
            'quantity' => 0,
        ]);
        $this->assertEquals(0, $stock->quantity);
    }

    public function test_get_or_create_stock_returns_existing()
    {
        Stock::factory()->create(['product_id' => $this->product->id, 'size' => 'XXL', 'quantity' => 99]);

        $stock = $this->stockService->getOrCreateStock($this->product, 'XXL');

        $this->assertEquals(99, $stock->quantity);
    }

    public function test_preview_in_throws_on_zero_quantity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stockService->previewIn($this->product, 'M', 0);
    }

    public function test_stock_in_throws_on_zero_quantity()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->stockService->stockIn($this->product, 'M', 0);
    }

    public function test_multiple_stock_ops_on_same_product()
    {
        $this->stockService->stockIn($this->product, 'M', 20);
        $this->stockService->stockOut($this->product, 'M', 5);
        $this->stockService->stockIn($this->product, 'M', 10);

        $stock = Stock::where('product_id', $this->product->id)->where('size', 'M')->first();
        $this->assertEquals(25, $stock->quantity);
    }

    public function test_transaction_audit_trail()
    {
        Stock::factory()->create(['product_id' => $this->product->id, 'size' => 'M', 'quantity' => 100]);
        $this->stockService->stockOut($this->product, 'M', 30);

        $this->assertEquals(1, StockTransaction::count());
    }
}
