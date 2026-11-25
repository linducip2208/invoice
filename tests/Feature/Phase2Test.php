<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\TimeEntry;
use App\Models\User;
use App\Models\Workspace;
use App\Services\QuoteConversionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase2Test extends TestCase
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
        $this->giveBusinessPlan($this->ws);
        session(['current_workspace_id' => $this->ws->id]);
        $this->client = Client::factory()->create(['workspace_id' => $this->ws->id]);
    }

    #[Test] public function time_entries_become_draft_invoice(): void
    {
        $a = TimeEntry::create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'user_id' => $this->user->id, 'description' => 'Dev', 'hours' => 2, 'hourly_rate' => 100000, 'entry_date' => now(), 'billable' => true]);
        $b = TimeEntry::create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'user_id' => $this->user->id, 'description' => 'Design', 'hours' => 1, 'hourly_rate' => 150000, 'entry_date' => now(), 'billable' => true]);

        $r = $this->actingAs($this->user)->post(route('time-entries.invoice'), ['ids' => [$a->id, $b->id]]);
        $r->assertRedirect();
        $inv = Invoice::latest()->first();
        $this->assertEquals(350000, (float) $inv->total);
        $this->assertEquals('draft', $inv->status);
        $this->assertTrue($a->fresh()->invoiced);
        $this->assertEquals($inv->id, $b->fresh()->invoice_id);
    }

    #[Test] public function time_invoice_rejects_mixed_clients(): void
    {
        $other = Client::factory()->create(['workspace_id' => $this->ws->id]);
        $a = TimeEntry::create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'user_id' => $this->user->id, 'description' => 'Dev', 'hours' => 2, 'hourly_rate' => 100000, 'entry_date' => now(), 'billable' => true]);
        $b = TimeEntry::create(['workspace_id' => $this->ws->id, 'client_id' => $other->id, 'user_id' => $this->user->id, 'description' => 'Ops', 'hours' => 1, 'hourly_rate' => 50000, 'entry_date' => now(), 'billable' => true]);

        $this->actingAs($this->user)->post(route('time-entries.invoice'), ['ids' => [$a->id, $b->id]])
            ->assertSessionHas('error');
        $this->assertEquals(0, Invoice::count());
    }

    #[Test] public function quote_down_payment_creates_half_invoice(): void
    {
        $quote = Quote::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'quote_no' => 'Q-TEST-1', 'status' => 'accepted', 'revision' => 1,
            'issue_date' => now(), 'currency' => 'IDR', 'tax_rate' => 0,
            'subtotal' => 1000000, 'tax_amount' => 0,
            'discount_type' => 'none', 'discount_value' => 0, 'discount_amount' => 0,
            'total' => 1000000, 'items' => [],
        ]);

        $r = $this->actingAs($this->user)->post(route('quotes.down-payment', $quote), ['percent' => 50]);
        $r->assertRedirect();
        $inv = Invoice::where('quote_conversion_type', 'down_payment')->first();
        $this->assertNotNull($inv);
        $this->assertEquals(500000, (float) $inv->total);

        // Idempoten: DP kedua balikin invoice yg sama.
        $r2 = $this->actingAs($this->user)->post(route('quotes.down-payment', $quote), ['percent' => 50]);
        $r2->assertRedirect(route('invoices.show', $inv));
        $this->assertEquals(1, Invoice::where('quote_conversion_type', 'down_payment')->count());
    }

    #[Test] public function quote_dp_rejects_non_accepted(): void
    {
        $quote = Quote::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'quote_no' => 'Q-TEST-2', 'status' => 'draft', 'revision' => 1,
            'issue_date' => now(), 'currency' => 'IDR', 'tax_rate' => 0,
            'subtotal' => 1000000, 'tax_amount' => 0,
            'discount_type' => 'none', 'discount_value' => 0, 'discount_amount' => 0,
            'total' => 1000000, 'items' => [],
        ]);

        $svc = app(QuoteConversionService::class);
        $res = $svc->convertDownPayment($quote, 30, 'test');
        $this->assertArrayHasKey('error', $res);

        $res2 = $svc->convertDownPayment($quote, 0, 'test');
        $this->assertArrayHasKey('error', $res2);
    }

    #[Test] public function cashflow_shows_outflow_and_net(): void
    {
        $this->seedPlans();
        \App\Models\Expense::create(['workspace_id' => $this->ws->id, 'description' => 'Sewa', 'amount' => 3000000, 'currency' => 'IDR', 'expense_date' => now()->subMonth()->toDateString()]);
        $r = $this->actingAs($this->user)->get(route('reports.cashflow'));
        $r->assertOk();
        $r->assertSee('Net Proyeksi', false);
        $r->assertSee('Proyeksi Biaya', false);
    }

    #[Test] public function reconcile_suggestions_render(): void
    {
        $inv = Invoice::factory()->withTotal(250000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'balance_due' => 250000,
        ]);
        $batch = \App\Models\BankReconcileBatch::create([
            'workspace_id' => $this->ws->id, 'bank_name' => 'BCA', 'account_no' => '123',
            'file_path' => 'test.csv', 'file_format' => 'csv',
            'imported_by' => $this->user->id, 'total_lines' => 1, 'status' => 'review',
        ]);
        $line = $batch->lines()->create([
            'workspace_id' => $this->ws->id,
            'trx_date' => now()->toDateString(),
            'amount' => 250000,
            'description' => 'TRF ' . $this->client->name . ' ' . $inv->invoice_no,
        ]);

        $r = $this->actingAs($this->user)->get(route('bank-reconcile.show', $batch));
        $r->assertOk();
        $r->assertSee($inv->invoice_no, false);
    }

    #[Test] public function whatsapp_templates_render(): void
    {
        $inv = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
        ]);
        $r = $this->actingAs($this->user)->get(route('invoices.whatsapp.preview', $inv));
        $r->assertOk();
        $r->assertSee('Template Pesan', false);
        $r->assertSee('Pengingat H-3', false);
    }
}
