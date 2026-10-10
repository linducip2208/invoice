<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WebhookTest extends TestCase
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
        session(['current_workspace_id' => $this->workspace->id]);
        $this->client = Client::factory()->create(['workspace_id' => $this->workspace->id]);
    }

    #[Test]
    public function payment_webhook_unknown_transaction_returns_404(): void
    {
        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'NOPE-123',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function payment_webhook_settles_pending_payment(): void
    {
        $invoice = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'amount' => 220000,
            'gateway_transaction_id' => 'INV-1-123456',
            'gateway_status' => 'pending',
        ]);

        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'INV-1-123456',
            'transaction_status' => 'settlement',
            'gross_amount' => '220000.00',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'gateway_status' => 'settlement']);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid', 'balance_due' => 0]);
    }

    #[Test]
    public function payment_webhook_replay_does_not_double_settle(): void
    {
        $invoice = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
            'status' => 'paid',
            'amount_paid' => 220000,
            'balance_due' => 0,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'amount' => 220000,
            'gateway_transaction_id' => 'INV-9-999',
            'gateway_status' => 'settlement',
        ]);

        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'INV-9-999',
            'transaction_status' => 'settlement',
            'gross_amount' => '220000.00',
        ]);

        $response->assertOk();
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(220000, (float) $invoice->amount_paid);
    }

    #[Test]
    public function payment_webhook_does_not_trust_request_amount(): void
    {
        $invoice = Invoice::factory()->withTotal(200000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'amount' => 220000,
            'gateway_transaction_id' => 'INV-2-222',
            'gateway_status' => 'pending',
        ]);

        // Attacker claims a token settlement amount; stored amount must win.
        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'INV-2-222',
            'transaction_status' => 'settlement',
            'gross_amount' => '1.00',
        ]);

        $response->assertOk();
        $payment->refresh();
        $this->assertEquals(220000, (float) $payment->amount);
    }

    #[Test]
    public function billing_webhook_replay_does_not_extend_twice(): void
    {
        $plan = Plan::where('slug', 'pro')->firstOrFail();
        $sub = Subscription::create([
            'workspace_id' => $this->workspace->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_period' => 'monthly',
            'currency' => 'IDR',
            'external_subscription_id' => 'sub_ext_1',
        ]);
        SubscriptionInvoice::create([
            'subscription_id' => $sub->id,
            'workspace_id' => $this->workspace->id,
            'external_invoice_id' => 'inv_ext_1',
            'reference_no' => 'SUB-1-test',
            'amount' => 99000,
            'currency' => 'IDR',
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $payload = [
            'subscription_id' => 'sub_ext_1',
            'type' => 'invoice.paid',
            'invoice_id' => 'inv_ext_1',
            'amount' => 99000,
        ];

        $this->postJson(route('webhooks.billing', ['gateway' => 'generic']), $payload)->assertOk();
        // Replay the same delivery.
        $this->postJson(route('webhooks.billing', ['gateway' => 'generic']), $payload)->assertOk();

        $this->assertEquals(1, SubscriptionInvoice::where('subscription_id', $sub->id)
            ->where('external_invoice_id', 'inv_ext_1')
            ->where('status', 'paid')
            ->count());
    }

    // ─── Signature verification (fake secrets, real algorithms) ─────────

    protected function makeGateway(string $gateway, array $secrets): PaymentGateway
    {
        $gw = new PaymentGateway([
            'workspace_id' => $this->workspace->id,
            'name' => 'Test ' . $gateway,
            'gateway' => $gateway,
            'base_url' => 'https://example.test',
            'is_active' => true,
        ]);
        foreach ($secrets as $key => $value) {
            $gw->setNonEncryptedKey($key, $value);
        }
        $gw->save();
        return $gw;
    }

    #[Test]
    public function midtrans_webhook_with_valid_signature_settles(): void
    {
        $gw = $this->makeGateway('midtrans', ['server_key' => 'fake-server-key']);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'payment_gateway_id' => $gw->id,
            'amount' => 110000,
            'gateway_transaction_id' => 'MT-ORDER-1',
            'gateway_status' => 'pending',
        ]);

        $payload = [
            'order_id' => 'MT-ORDER-1',
            'status_code' => '200',
            'gross_amount' => '110000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash('sha512', 'MT-ORDER-1' . '200' . '110000.00' . 'fake-server-key');

        $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), $payload)->assertOk();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'gateway_status' => 'settlement']);
    }

    #[Test]
    public function midtrans_webhook_with_invalid_signature_is_rejected(): void
    {
        $gw = $this->makeGateway('midtrans', ['server_key' => 'fake-server-key']);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'payment_gateway_id' => $gw->id,
            'amount' => 110000,
            'gateway_transaction_id' => 'MT-ORDER-2',
            'gateway_status' => 'pending',
        ]);

        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'MT-ORDER-2',
            'status_code' => '200',
            'gross_amount' => '110000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'forged-signature',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'gateway_status' => 'pending']);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'draft']);
    }

    #[Test]
    public function midtrans_webhook_with_tampered_amount_is_rejected(): void
    {
        $gw = $this->makeGateway('midtrans', ['server_key' => 'fake-server-key']);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'payment_gateway_id' => $gw->id,
            'amount' => 110000,
            'gateway_transaction_id' => 'MT-ORDER-3',
            'gateway_status' => 'pending',
        ]);

        // Signature computed for 110000 but body claims 1.00.
        $response = $this->postJson(route('webhooks.payment', ['gateway' => 'midtrans']), [
            'order_id' => 'MT-ORDER-3',
            'status_code' => '200',
            'gross_amount' => '1.00',
            'transaction_status' => 'settlement',
            'signature_key' => hash('sha512', 'MT-ORDER-3' . '200' . '110000.00' . 'fake-server-key'),
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'gateway_status' => 'pending']);
    }

    #[Test]
    public function xendit_webhook_token_is_enforced(): void
    {
        $gw = $this->makeGateway('xendit', ['api_key' => 'fake-callback-token']);
        $invoice = Invoice::factory()->withTotal(50000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->workspace->id,
            'invoice_id' => $invoice->id,
            'payment_gateway_id' => $gw->id,
            'amount' => 55000,
            'gateway_transaction_id' => 'EXT-1',
            'gateway_status' => 'PENDING',
        ]);

        $this->postJson(route('webhooks.payment', ['gateway' => 'xendit']), [
            'external_id' => 'EXT-1',
            'status' => 'PAID',
            'amount' => 55000,
        ], ['X-CALLBACK-TOKEN' => 'wrong-token'])->assertForbidden();

        $this->postJson(route('webhooks.payment', ['gateway' => 'xendit']), [
            'external_id' => 'EXT-1',
            'status' => 'PAID',
            'amount' => 55000,
        ], ['X-CALLBACK-TOKEN' => 'fake-callback-token'])->assertOk();

        $this->assertDatabaseHas('payments', ['id' => $payment->id, 'gateway_status' => 'PAID']);
    }
}
