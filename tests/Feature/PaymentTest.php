<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->workspace->id, ['role' => 'admin']);
        $this->giveProPlan($this->workspace);
        session(['current_workspace_id' => $this->workspace->id]);
        $this->client = Client::factory()->create(['workspace_id' => $this->workspace->id]);
    }

    #[Test]
    public function user_can_view_payments(): void
    {
        $invoice = Invoice::factory()->paid()->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        Payment::factory()->count(3)->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('payments.index'));

        $response->assertStatus(200);
        // Locale-aware: views render __('Payments') (e.g. "Pembayaran").
        $response->assertSee(__('Payments'));
    }

    #[Test]
    public function user_can_record_payment(): void
    {
        $invoice = Invoice::factory()->withTotal(500000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('payments.store', $invoice), [
                'amount' => 500000,
                'payment_date' => now()->format('Y-m-d'),
                'payment_method' => 'bank_transfer',
                'reference_no' => 'PAY-TEST-001',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'amount' => 500000,
        ]);
    }

    #[Test] public function payment_updates_invoice_balance(): void
    {
        $invoice = Invoice::factory()->withTotal(300000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);

        $this->actingAs($this->user)->post(route('payments.store', $invoice), [
            'amount' => 100000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'bank_transfer',
        ]);

        $invoice->refresh();
        $this->assertGreaterThan(0, $invoice->amount_paid);
        $this->assertLessThan($invoice->total, $invoice->balance_due);
    }

    #[Test] public function full_payment_marks_invoice_paid(): void
    {
        $invoice = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);

        // withTotal(200000) builds subtotal 200000 + 10% tax = total 220000.
        // Pay the FULL balance_due, not the subtotal.
        $invoice->refresh();
        $fullAmount = (float) $invoice->balance_due;

        $this->actingAs($this->user)->post(route('payments.store', $invoice), [
            'amount' => $fullAmount,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method' => 'cash',
        ]);

        $invoice->refresh();
        $this->assertEquals(0, (float) $invoice->balance_due);
        $this->assertEquals('paid', $invoice->status);
    }

    #[Test]
    public function user_can_delete_payment(): void
    {
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'amount' => 50000,
        ]);

        $response = $this->actingAs($this->user)->delete(route('payments.destroy', $payment));

        $this->assertDatabaseMissing('payments', ['id' => $payment->id]);
    }
}
