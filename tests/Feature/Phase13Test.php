<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\ScheduledInvoiceSend;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase13Test extends TestCase
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
        Mail::fake();
    }

    #[Test] public function scheduled_send_runs_via_command(): void
    {
        $inv = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'sent',
        ]);

        $this->actingAs($this->user)->post(route('invoices.schedule', $inv), [
            'to_email' => 'klien@contoh.id', 'subject' => 'Invoice INV', 'message' => 'Halo',
            'send_at' => now()->addHour()->format('Y-m-d\TH:i'),
        ])->assertSessionHas('success');

        $job = ScheduledInvoiceSend::first();
        $this->assertNotNull($job);
        $this->assertEquals('pending', $job->status);

        // Belum jatuh tempo → command tidak mengirim.
        $this->artisan('invoices:send-scheduled')->assertSuccessful();
        $this->assertEquals('pending', $job->fresh()->status);

        // Majukan waktu → terkirim.
        $job->update(['send_at' => now()->subMinute()]);
        $this->artisan('invoices:send-scheduled')->assertSuccessful();
        $this->assertEquals('sent', $job->fresh()->status);
        $this->assertNotNull($job->fresh()->sent_at);
    }

    #[Test] public function schedule_rejects_past_and_paid(): void
    {
        $inv = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'paid',
        ]);

        $this->actingAs($this->user)->post(route('invoices.schedule', $inv), [
            'to_email' => 'k@x.id', 'subject' => 'x',
            'send_at' => now()->subHour()->format('Y-m-d\TH:i'),
        ])->assertSessionHasErrors('send_at');
        $this->assertEquals(0, ScheduledInvoiceSend::count());
    }

    #[Test] public function credit_limit_blocks_over_limit_invoice(): void
    {
        $this->client->update(['credit_limit' => 100000]);
        Invoice::factory()->withTotal(80000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'amount_paid' => 0, 'balance_due' => 80000,
        ]);

        $payload = $this->invoicePayload(50000);
        $this->actingAs($this->user)->post(route('invoices.store'), $payload)
            ->assertSessionHas('error');
        $this->assertEquals(1, Invoice::count());
    }

    #[Test] public function credit_limit_allows_within_limit(): void
    {
        $this->client->update(['credit_limit' => 1000000]);

        $payload = $this->invoicePayload(50000);
        $this->actingAs($this->user)->post(route('invoices.store'), $payload)
            ->assertRedirect();
        $this->assertEquals(1, Invoice::count());
    }

    #[Test] public function report_pdfs_download(): void
    {
        foreach (['reports.index', 'reports.aging', 'reports.cashflow', 'reports.pl', 'reports.tax'] as $name) {
            $r = $this->actingAs($this->user)->get(route($name, ['format' => 'pdf']));
            $r->assertOk();
            $this->assertStringContainsString('pdf', $r->headers->get('Content-Type'));
        }
    }

    private function invoicePayload(int $unitPrice): array
    {
        return [
            'client_id' => $this->client->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'template' => 'classic', 'currency' => 'IDR',
            'discount_type' => 'none',
            'items' => [['description' => 'Jasa', 'quantity' => 1, 'unit_price' => $unitPrice]],
        ];
    }
}
