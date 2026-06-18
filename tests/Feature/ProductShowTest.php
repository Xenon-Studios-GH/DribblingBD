<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteProject;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_show_page_loads()
    {
        // Setup: Create a product and a project
        $product = Product::factory()->create(['is_active' => true]);
        $project = WebsiteProject::create([
            'product_id' => $product->id,
            'slug' => 'test-product',
            'is_active' => true,
            'created_by' => User::factory()->create()->id,
        ]);

        $response = $this->get(route('shop.products.show', $project));

        $response->assertStatus(200);
    }
}
