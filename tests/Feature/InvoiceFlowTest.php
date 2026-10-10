<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * End-to-end business flow:
 * client -> invoice -> reload/detail -> PDF -> mail fake -> partial ->
 * final payment -> paid -> report reflects.
 */
class InvoiceFlowTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function full_invoice_lifecycle(): void
    {
        Mail::fake();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->seedPlans();
        $this->giveProPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $a = $this->actingAs($user);

        // 1. Create client.
        $a->post(route('clients.store'), ['name' => 'Flow Client', 'is_active' => '1'])
            ->assertRedirect();
        $client = Client::where('workspace_id', $ws->id)->where('name', 'Flow Client')->firstOrFail();

        // 2. Create invoice (2 x 100000, tax 10% => total 220000).
        $product = Product::factory()->create(['workspace_id' => $ws->id]);
        $a->post(route('invoices.store'), [
            'client_id' => $client->id, 'currency' => 'IDR',
            'issue_date' => now()->format('Y-m-d'), 'due_date' => now()->addDays(14)->format('Y-m-d'),
            'template' => 'classic', 'tax_rate' => 10, 'discount_type' => 'none', 'discount_value' => 0,
            'items' => [['product_id' => $product->id, 'description' => 'Service', 'quantity' => 2, 'unit_price' => 100000, 'tax_rate' => 0]],
        ])->assertRedirect();
        $invoice = Invoice::where('workspace_id', $ws->id)->firstOrFail();
        $this->assertEquals(220000, (float) $invoice->total);
        $this->assertEquals('draft', $invoice->status);

        // 3. Reload detail persists.
        $a->get(route('invoices.show', $invoice))->assertOk()->assertSee($invoice->invoice_no);

        // 4. Send + PDF endpoints respond.
        $a->post(route('invoices.update-status', $invoice), ['status' => 'sent'])->assertRedirect();
        $a->get(route('invoices.pdf', $invoice))->assertOk();
        $a->post(route('invoices.send', $invoice), ['email' => 'client@example.test'])->assertRedirect();

        // 5. Partial then final payment.
        $pay = fn ($amount) => $a->post(route('payments.store', $invoice), [
            'amount' => $amount, 'payment_date' => now()->format('Y-m-d'), 'payment_method' => 'bank_transfer',
        ])->assertRedirect();
        $pay(100000);
        $this->assertEquals('partial', $invoice->fresh()->status);
        $pay(120000);
        $invoice->refresh();
        $this->assertEquals('paid', $invoice->status);
        $this->assertEquals(0, (float) $invoice->balance_due);

        // 6. Reports reflect the paid invoice.
        $a->get(route('reports.index'))->assertOk();
        $a->get(route('reports.aging'))->assertOk();
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => 'paid']);
    }
}
