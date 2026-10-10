<?php

namespace Tests\Feature;

use App\Mail\InvoiceMail;
use App\Models\Client;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OpsTest extends TestCase
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
        $this->client = Client::factory()->create([
            'workspace_id' => $this->ws->id, 'email' => 'ops@example.test',
        ]);
        Mail::fake();
    }

    #[Test] public function expired_quotes_marked_by_scheduler(): void
    {
        $q = Quote::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'quote_no' => 'Q-' . strtoupper(Str::random(8)), 'status' => 'sent', 'revision' => 1,
            'issue_date' => now()->subDays(30)->toDateString(),
            'expiry_date' => now()->subDay()->toDateString(),
            'currency' => 'IDR', 'tax_rate' => 0, 'subtotal' => 1000, 'tax_amount' => 0,
            'total' => 1000, 'items' => [],
        ]);
        $this->assertEquals(0, Artisan::call('quotes:mark-expired'));
        $this->assertEquals('expired', $q->fresh()->status);
    }

    #[Test] public function invoice_send_queues_mail_and_logs(): void
    {
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
        ]);
        $this->actingAs($this->user)->post(route('invoices.send', $invoice), [
            'subject' => 'Invoice test', 'message' => 'Hi', 'to_email' => 'ops@example.test',
        ])->assertRedirect();

        Mail::assertQueued(InvoiceMail::class, fn ($m) => true);
        $this->assertDatabaseHas('email_logs', [
            'invoice_id' => $invoice->id, 'to_email' => 'ops@example.test', 'status' => 'queued',
        ]);
        $this->assertEquals('sent', $invoice->fresh()->status);
    }

    #[Test] public function invoice_pdf_is_valid_pdf(): void
    {
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
        ]);
        $response = $this->actingAs($this->user)->get(route('invoices.pdf', $invoice));
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF', $response->streamedContent() ?? $response->getContent());
    }
}
