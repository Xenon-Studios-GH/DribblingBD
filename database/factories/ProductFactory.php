<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_code' => 'Dribbling-' . $this->faker->unique()->randomNumber(4, true),
            'product_name' => fake()->words(2, true) . ' Jersey',
            'price' => fake()->randomFloat(2, 10, 99),
            'is_active' => true,
        ];
    }
}
