<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\CreditNote;
use App\Models\Invoice;
use App\Models\Quote;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase3Test extends TestCase
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

    private function makeQuote(string $no, string $status, ?string $expiry): Quote
    {
        return Quote::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'quote_no' => $no, 'status' => $status, 'revision' => 1,
            'issue_date' => now(), 'expiry_date' => $expiry, 'currency' => 'IDR',
            'tax_rate' => 0, 'subtotal' => 100000, 'tax_amount' => 0,
            'discount_type' => 'none', 'discount_value' => 0, 'discount_amount' => 0,
            'total' => 100000, 'items' => [],
        ]);
    }

    #[Test] public function quotes_expiring_filter(): void
    {
        $soon = $this->makeQuote('Q-SOON', 'sent', now()->addDays(3)->toDateString());
        $later = $this->makeQuote('Q-LATER', 'sent', now()->addDays(30)->toDateString());

        $r = $this->actingAs($this->user)->get(route('quotes.index', ['expiring' => 1]));
        $r->assertOk();
        $r->assertSee('Q-SOON', false);
        $r->assertDontSee('Q-LATER', false);
    }

    #[Test] public function bulk_send_queues_and_marks_sent(): void
    {
        $a = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'draft']);
        $b = Invoice::factory()->withTotal(200000)->create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id, 'status' => 'draft']);

        $this->actingAs($this->user)->post(route('invoices.bulk-send'), ['ids' => [$a->id, $b->id]])
            ->assertSessionHas('success');

        $this->assertEquals(2, \App\Models\EmailLog::whereIn('invoice_id', [$a->id, $b->id])->count());
        $this->assertEquals('sent', $a->fresh()->status);
    }

    #[Test] public function bulk_send_skips_no_email(): void
    {
        $noMail = Client::factory()->create(['workspace_id' => $this->ws->id, 'email' => null]);
        $inv = Invoice::factory()->withTotal(50000)->create(['workspace_id' => $this->ws->id, 'client_id' => $noMail->id, 'status' => 'draft']);

        $this->actingAs($this->user)->post(route('invoices.bulk-send'), ['ids' => [$inv->id]])
            ->assertSessionHas('success');
        $this->assertEquals(0, \App\Models\EmailLog::where('invoice_id', $inv->id)->count());
        $this->assertEquals('draft', $inv->fresh()->status);
    }

    #[Test] public function credit_issue_and_apply_one_click(): void
    {
        $inv = Invoice::factory()->withTotal(500000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'sent', 'balance_due' => 500000,
        ]);
        $cn = CreditNote::create([
            'workspace_id' => $this->ws->id, 'invoice_id' => $inv->id,
            'credit_no' => 'CN-TEST-1', 'reason' => 'Diskon', 'amount' => 100000,
            'currency' => 'IDR', 'issued_at' => now(), 'status' => 'draft',
            'created_by' => $this->user->id,
        ]);

        $this->actingAs($this->user)->post(route('credit-notes.issue', $cn), ['apply' => 1])
            ->assertSessionHas('success');

        $this->assertEquals('applied', $cn->fresh()->status);
        $this->assertEquals(400000, (float) $inv->fresh()->balance_due);
    }
}
