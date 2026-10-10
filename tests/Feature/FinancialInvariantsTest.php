<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use App\Services\LateFee\LateFeeApplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Financial invariants: totals derive from lines; balances reconcile across
 * payments, credit notes, and late fees.
 */
class FinancialInvariantsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $ws;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->user = User::factory()->create();
        $this->ws = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->ws->id, ['role' => 'admin']);
        $this->giveProPlan($this->ws);
        session(['current_workspace_id' => $this->ws->id]);
        $this->client = Client::factory()->create(['workspace_id' => $this->ws->id]);
    }

    #[Test] public function invoice_total_derives_from_lines(): void
    {
        $response = $this->actingAs($this->user)->post(route('invoices.store'), [
            'client_id' => $this->client->id, 'currency' => 'IDR',
            'issue_date' => now()->format('Y-m-d'), 'due_date' => now()->addDays(7)->format('Y-m-d'),
            'template' => 'classic', 'tax_rate' => 10, 'discount_type' => 'percent', 'discount_value' => 5,
            'items' => [
                ['description' => 'A', 'quantity' => 2, 'unit_price' => 100000, 'tax_rate' => 0],
                ['description' => 'B', 'quantity' => 1, 'unit_price' => 50000, 'tax_rate' => 0],
            ],
        ]);
        $response->assertRedirect();
        $inv = Invoice::where('workspace_id', $this->ws->id)->firstOrFail();
        // subtotal 250000, tax 25000, discount 12500 => total 262500.
        $this->assertEquals(250000, (float) $inv->subtotal);
        $this->assertEquals(25000, (float) $inv->tax_amount);
        $this->assertEquals(12500, (float) $inv->discount_amount);
        $this->assertEquals(262500, (float) $inv->total);
        $this->assertEquals(262500, (float) $inv->balance_due);
        $this->assertEquals(250000, (float) $inv->items()->sum('subtotal'));
    }

    #[Test] public function balance_tracks_partial_payments(): void
    {
        $inv = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
        ]); // total 220000
        $this->actingAs($this->user)->post(route('payments.store', $inv), [
            'amount' => 120000, 'payment_date' => now()->format('Y-m-d'), 'payment_method' => 'cash',
        ]);
        $inv->refresh();
        $this->assertEquals(120000, (float) $inv->amount_paid);
        $this->assertEquals(100000, (float) $inv->balance_due);
        $this->assertEquals('partial', $inv->status);
    }

    #[Test] public function credit_apply_and_cancel_reconcile(): void
    {
        $inv = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'sent',
        ]); // balance 220000
        $cn = CreditNote::create([
            'workspace_id' => $this->ws->id, 'invoice_id' => $inv->id,
            'credit_no' => 'CN-TEST-1', 'reason' => 'test', 'amount' => 20000,
            'currency' => 'IDR', 'issued_at' => now()->toDateString(), 'status' => 'issued',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('credit-notes.apply', $cn));
        $inv->refresh();
        // Documented invariant: credit reduces balance, amount_paid untouched.
        $this->assertEquals(200000, (float) $inv->balance_due);
        $this->assertEquals(0, (float) $inv->amount_paid);

        $this->actingAs($this->user)->post(route('credit-notes.cancel', $cn));
        $this->assertEquals(220000, (float) $inv->fresh()->balance_due);
    }

    #[Test] public function late_fee_applies_once_and_reverses(): void
    {
        $inv = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'due_date' => now()->subDays(10)->toDateString(),
            'late_fee_policy' => 'one_time', 'late_fee_type' => 'percent', 'late_fee_value' => 5,
        ]); // balance 110000 -> fee 5500
        $svc = app(LateFeeApplier::class);

        $fee = $svc->apply($inv->fresh());
        $this->assertNotNull($fee);
        $this->assertEquals(5500, (float) $fee->amount);
        $this->assertEquals(115500, (float) $inv->fresh()->balance_due);

        // Second application is refused by policy (no compounding).
        $this->assertNull($svc->apply($inv->fresh()));
        $this->assertEquals(115500, (float) $inv->fresh()->balance_due);

        $svc->reverse($fee);
        $this->assertEquals(110000, (float) $inv->fresh()->balance_due);
    }
}
