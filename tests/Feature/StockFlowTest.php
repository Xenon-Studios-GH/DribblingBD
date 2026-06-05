<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_stock_management_page_loads()
    {
        Product::factory()->count(3)->hasStocks(1)->create();

        $response = $this->actingAs($this->user)->get(route('stock.management'));
        $response->assertStatus(200);
    }

    public function test_stock_in_page_loads()
    {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('stock.in'));
        $response->assertStatus(200);
    }

    public function test_stock_out_page_loads()
    {
        Product::factory()->count(3)->hasStocks(1)->create();

        $response = $this->actingAs($this->user)->get(route('stock.out'));
        $response->assertStatus(200);
    }

    public function test_stock_in_preview()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->post(route('stock.in.preview'), [
            'product_id' => $product->id,
            'size' => 'M',
            'quantity' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'product_id' => $product->id,
            'size' => 'M',
            'current_stock' => 0,
            'change' => 10,
            'new_stock' => 10,
        ]);
    }

    public function test_stock_in_confirm()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)->post(route('stock.in.preview'), [
            'product_id' => $product->id,
            'size' => 'L',
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.in.confirm'), [
            'product_id' => $product->id,
            'size' => 'L',
            'quantity' => 5,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'size' => 'L',
            'quantity' => 5,
        ]);
    }

    public function test_stock_in_with_new_product()
    {
        $response = $this->actingAs($this->user)->post(route('stock.in.preview'), [
            'product_id' => 'new',
            'product_name' => 'Test Hoodie',
            'price' => 49.99,
            'size' => 'XL',
            'quantity' => 20,
        ]);
        $response->assertStatus(200);
        $this->assertEquals('new', $response->json('product_id'));

        $confirmResponse = $this->actingAs($this->user)->post(route('stock.in.confirm'), [
            'product_id' => 'new',
            'product_name' => 'Test Hoodie',
            'price' => 49.99,
            'size' => 'XL',
            'quantity' => 20,
        ]);
        $confirmResponse->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', ['product_name' => 'Test Hoodie']);
    }

    public function test_stock_out_confirm()
    {
        $product = Product::factory()->create();
        Stock::factory()->create(['product_id' => $product->id, 'size' => 'S', 'quantity' => 50]);

        $this->actingAs($this->user)->post(route('stock.out.preview'), [
            'product_id' => $product->id,
            'size' => 'S',
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.out.confirm'), [
            'product_id' => $product->id,
            'size' => 'S',
            'quantity' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('stocks', [
            'product_id' => $product->id,
            'size' => 'S',
            'quantity' => 40,
        ]);
    }

    public function test_stock_out_insufficient_stock()
    {
        $product = Product::factory()->create();
        Stock::factory()->create(['product_id' => $product->id, 'size' => 'M', 'quantity' => 5]);

        $response = $this->actingAs($this->user)->post(route('stock.out.preview'), [
            'product_id' => $product->id,
            'size' => 'M',
            'quantity' => 10,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
    }

    public function test_workers_page_blocked_for_staff()
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('workers.index'));
        $response->assertStatus(403);
    }

    public function test_workers_page_allowed_for_superadmin()
    {
        $admin = User::factory()->superadmin()->create();

        $response = $this->actingAs($admin)->get(route('workers.index'));
        $response->assertStatus(200);
    }

    public function test_stock_management_show_page_loads()
    {
        $product = Product::factory()->create();
        foreach (['S', 'M', 'L'] as $size) {
            Stock::factory()->create(['product_id' => $product->id, 'size' => $size]);
        }

        $response = $this->actingAs($this->user)->get(route('stock.management.show', $product));
        $response->assertStatus(200);
        $response->assertSee($product->product_name);
        $response->assertSee($product->product_code);
    }

    public function test_stock_in_confirm_returns_product_id()
    {
        $product = Product::factory()->create();

        $this->actingAs($this->user)->post(route('stock.in.preview'), [
            'product_id' => $product->id,
            'size' => 'L',
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.in.confirm'), [
            'product_id' => $product->id,
            'size' => 'L',
            'quantity' => 5,
        ]);

        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'product_id']);
    }

    public function test_stock_activity_page_loads()
    {
        $product = Product::factory()->create();
        $this->actingAs($this->user)->post(route('stock.in.preview'), [
            'product_id' => $product->id,
            'size' => 'M',
            'quantity' => 10,
        ]);
        $this->actingAs($this->user)->post(route('stock.in.confirm'), [
            'product_id' => $product->id,
            'size' => 'M',
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)->get(route('stock.activity'));
        $response->assertStatus(200);
        $response->assertSee('Recent Activity');
    }

    public function test_stock_out_confirm_returns_product_id()
    {
        $product = Product::factory()->create();
        Stock::factory()->create(['product_id' => $product->id, 'size' => 'S', 'quantity' => 50]);

        $this->actingAs($this->user)->post(route('stock.out.preview'), [
            'product_id' => $product->id,
            'size' => 'S',
            'quantity' => 10,
        ]);

        $response = $this->actingAs($this->user)->post(route('stock.out.confirm'), [
            'product_id' => $product->id,
            'size' => 'S',
            'quantity' => 10,
        ]);

        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['success', 'product_id']);
    }
}
