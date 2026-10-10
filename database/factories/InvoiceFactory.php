<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'client_id' => Client::factory(),
            'invoice_no' => 'INV-' . now()->format('Ymd') . '-' . str_pad((string) fake()->unique()->randomNumber(4), 4, '0', STR_PAD_LEFT),
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'subtotal' => 0,
            'tax_rate' => 10,
            'tax_amount' => 0,
            'discount_type' => 'none',
            'discount_value' => 0,
            'discount_amount' => 0,
            'total' => 0,
            'amount_paid' => 0,
            'balance_due' => 0,
            'status' => 'draft',
            'currency' => 'IDR',
            'template' => 'classic',
            'primary_color' => '#A78BFA',
            'accent_color' => '#FF7AC6',
            'notes' => null,
            'terms' => null,
        ];
    }

    public function sent(): static
    {
        return $this->state(fn() => ['status' => 'sent']);
    }

    public function paid(): static
    {
        return $this->state(fn() => [
            'status' => 'paid',
            'amount_paid' => 0,
            'balance_due' => 0,
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn() => [
            'status' => 'overdue',
            'due_date' => now()->subDays(10),
        ]);
    }

    public function withTotal(float $total): static
    {
        return $this->state(fn() => [
            'subtotal' => $total,
            'total' => $total * 1.1,
            'tax_amount' => $total * 0.1,
            'balance_due' => $total * 1.1,
            'amount_paid' => 0,
        ]);
    }
}
