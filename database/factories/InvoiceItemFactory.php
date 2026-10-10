<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => null,
            'description' => fake()->sentence(3),
            'quantity' => fake()->randomFloat(2, 1, 5),
            'unit_price' => fake()->randomFloat(2, 50000, 500000),
            'tax_rate' => 0,
        ];
    }
}
