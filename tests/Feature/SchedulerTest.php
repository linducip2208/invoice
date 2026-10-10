<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\RecurringInvoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SchedulerTest extends TestCase
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
        $this->client = Client::factory()->create(['workspace_id' => $this->ws->id]);
    }

    #[Test] public function escalate_marks_past_due_sent_as_overdue(): void
    {
        $inv = Invoice::factory()->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'due_date' => now()->subDays(3)->toDateString(),
        ]);
        $this->assertEquals(0, Artisan::call('invoices:escalate-overdue'));
        $this->assertEquals('overdue', $inv->fresh()->status);
    }

    #[Test] public function escalate_leaves_paid_and_draft_alone(): void
    {
        $paid = Invoice::factory()->paid()->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'due_date' => now()->subDays(30)->toDateString(),
        ]);
        $draft = Invoice::factory()->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'draft', 'due_date' => now()->subDays(30)->toDateString(),
        ]);
        Artisan::call('invoices:escalate-overdue');
        $this->assertEquals('paid', $paid->fresh()->status);
        $this->assertEquals('draft', $draft->fresh()->status);
    }

    #[Test] public function recurring_generates_exactly_one_invoice_per_due_schedule(): void
    {
        $rec = RecurringInvoice::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'active', 'frequency' => 'monthly', 'interval_value' => 1,
            'next_run_date' => now()->subDay()->toDateString(),
            'invoice_data' => [
                'items' => [['description' => 'Retainer', 'quantity' => 1, 'unit_price' => 500000, 'tax_rate' => 0]],
                'tax_rate' => 0, 'discount_type' => 'none', 'discount_value' => 0, 'currency' => 'IDR',
            ],
        ]);
        $this->assertEquals(0, Artisan::call('invoices:generate-recurring'));
        $this->assertEquals(1, Invoice::where('recurring_invoice_id', $rec->id)->count());
        $generated = Invoice::where('recurring_invoice_id', $rec->id)->first();
        $this->assertEquals(500000, (float) $generated->total);
        // Second run must not duplicate (next_run_date advanced past today).
        $this->assertEquals(0, Artisan::call('invoices:generate-recurring'));
        $this->assertEquals(1, Invoice::where('recurring_invoice_id', $rec->id)->count());
    }

    #[Test] public function late_fees_dry_run_changes_nothing(): void
    {
        Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'due_date' => now()->subDays(10)->toDateString(),
            'late_fee_policy' => 'one_time', 'late_fee_type' => 'fixed', 'late_fee_value' => 10000,
        ]);
        $this->assertEquals(0, Artisan::call('late-fees:apply', ['--dry-run' => true]));
        $this->assertEquals(0, \App\Models\InvoiceLateFee::count());
    }
}
