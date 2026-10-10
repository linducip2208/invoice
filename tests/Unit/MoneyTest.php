<?php

namespace Tests\Unit;

use App\Services\InvoiceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Money math invariants for the single source of truth:
 * InvoiceService::calculateTotals().
 */
class MoneyTest extends TestCase
{
    #[Test]
    public function subtotal_sums_quantity_times_price(): void
    {
        $t = InvoiceService::calculateTotals([
            ['quantity' => 2, 'unit_price' => 100000],
            ['quantity' => 1.5, 'unit_price' => 50000],
        ], 0, 'none', 0);

        $this->assertEquals(275000, $t['subtotal']);
        $this->assertEquals(275000, $t['total']);
        $this->assertEquals(275000, $t['balance_due']);
    }

    #[Test]
    public function percent_tax_and_discount_round_to_cents(): void
    {
        $t = InvoiceService::calculateTotals([
            ['quantity' => 3, 'unit_price' => 33333.33],
        ], 11, 'percent', 10);

        // subtotal 99999.99; tax 11000.00 (10999.9989 rounded); discount 10000.00
        $this->assertEquals(99999.99, $t['subtotal']);
        $this->assertEquals(11000.00, $t['tax_amount']);
        $this->assertEquals(10000.00, $t['discount_amount']);
        $this->assertEquals(100999.99, $t['total']);
    }

    #[Test]
    public function fixed_discount_applies_verbatim(): void
    {
        $t = InvoiceService::calculateTotals([
            ['quantity' => 1, 'unit_price' => 200000],
        ], 0, 'fixed', 25000);

        $this->assertEquals(25000, $t['discount_amount']);
        $this->assertEquals(175000, $t['total']);
    }

    #[Test]
    public function fractional_quantities_keep_precision(): void
    {
        $t = InvoiceService::calculateTotals([
            ['quantity' => 0.1, 'unit_price' => 0.2],
        ], 0, 'none', 0);

        $this->assertEquals(0.02, $t['subtotal']);
    }

    #[Test]
    public function empty_items_yield_zero_totals(): void
    {
        $t = InvoiceService::calculateTotals([], 11, 'percent', 5);

        $this->assertEquals(0, $t['subtotal']);
        $this->assertEquals(0, $t['tax_amount']);
        $this->assertEquals(0, $t['discount_amount']);
        $this->assertEquals(0, $t['total']);
    }
}
