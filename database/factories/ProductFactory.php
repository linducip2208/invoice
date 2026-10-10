<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'unit_price' => fake()->randomFloat(2, 50000, 1000000),
            'tax_rate' => 0,
            'is_active' => true,
        ];
    }
}
