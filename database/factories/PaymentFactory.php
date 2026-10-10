<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'invoice_id' => Invoice::factory(),
            'amount' => fake()->randomFloat(2, 10000, 500000),
            'payment_date' => now(),
            'payment_method' => 'bank_transfer',
            'reference_no' => 'PAY-' . fake()->unique()->randomNumber(4),
            'notes' => null,
        ];
    }
}
