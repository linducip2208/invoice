<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase7Test extends TestCase
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

    #[Test] public function bulk_remind_schedules_and_skips_paid(): void
    {
        $open = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'due_date' => now()->addDays(10),
        ]);
        $paid = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'paid', 'due_date' => now()->addDays(10),
        ]);

        $this->actingAs($this->user)->post(route('invoices.bulk-remind'), ['ids' => [$open->id, $paid->id]])
            ->assertSessionHas('success');

        $this->assertGreaterThan(0, \App\Models\InvoiceReminder::where('invoice_id', $open->id)->where('status', 'pending')->count());
        $this->assertEquals(0, \App\Models\InvoiceReminder::where('invoice_id', $paid->id)->count());
    }

    #[Test] public function manual_payment_creates_notification(): void
    {
        $inv = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'sent',
        ]);

        $this->actingAs($this->user)->post(route('payments.store', $inv), [
            'amount' => 50000, 'payment_date' => now()->toDateString(),
            'payment_method' => 'bank_transfer',
        ])->assertRedirect();

        $this->assertEquals(1, \App\Models\AppNotification::where('workspace_id', $this->ws->id)->where('type', 'payment')->count());
    }

    #[Test] public function reconcile_finalize_creates_notification(): void
    {
        $inv = Invoice::factory()->withTotal(250000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'balance_due' => 250000,
        ]);
        $batch = \App\Models\BankReconcileBatch::create([
            'workspace_id' => $this->ws->id, 'bank_name' => 'BCA', 'account_no' => '123',
            'file_path' => 't.csv', 'file_format' => 'csv',
            'imported_by' => $this->user->id, 'total_lines' => 1, 'status' => 'review',
        ]);
        $line = $batch->lines()->create([
            'workspace_id' => $this->ws->id, 'trx_date' => now()->toDateString(),
            'amount' => 250000, 'description' => 'TRF',
            'matched_invoice_id' => $inv->id, 'match_confidence' => 'manual', 'match_method' => 'manual',
        ]);

        $this->actingAs($this->user)->post(route('bank-reconcile.finalize', $batch))->assertSessionHas('success');
        $this->assertEquals(1, \App\Models\AppNotification::where('type', 'reconcile')->count());
    }
}
