<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Quote;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase4Test extends TestCase
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

    private function acceptedQuote(string $no, float $total = 1000000): Quote
    {
        return Quote::create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'quote_no' => $no, 'status' => 'accepted', 'revision' => 1,
            'issue_date' => now(), 'currency' => 'IDR', 'tax_rate' => 0,
            'subtotal' => $total, 'tax_amount' => 0,
            'discount_type' => 'none', 'discount_value' => 0, 'discount_amount' => 0,
            'total' => $total, 'items' => [],
        ]);
    }

    #[Test] public function remainder_needs_existing_dp(): void
    {
        $q = $this->acceptedQuote('Q-REM-0');
        $this->actingAs($this->user)->post(route('quotes.remainder', $q))
            ->assertSessionHas('error');
        $this->assertEquals(0, \App\Models\Invoice::count());
    }

    #[Test] public function dp_then_remainder_covers_full_total(): void
    {
        $q = $this->acceptedQuote('Q-REM-1');
        $this->actingAs($this->user)->post(route('quotes.down-payment', $q), ['percent' => 30])->assertRedirect();

        $r = $this->actingAs($this->user)->post(route('quotes.remainder', $q));
        $r->assertRedirect();
        $rem = \App\Models\Invoice::where('quote_conversion_type', 'remainder')->first();
        $this->assertNotNull($rem);
        $this->assertEquals(700000, (float) $rem->total);

        // Idempoten.
        $this->actingAs($this->user)->post(route('quotes.remainder', $q))->assertRedirect(route('invoices.show', $rem));
        $this->assertEquals(1, \App\Models\Invoice::where('quote_conversion_type', 'remainder')->count());
    }

    #[Test] public function nps_thanks_shows_review_cta_for_promoter(): void
    {
        $this->ws->update(['google_review_url' => 'https://g.page/contoh']);
        $nps = \App\Models\NpsResponse::create([
            'workspace_id' => $this->ws->id, 'score' => 10,
            'submitted_at' => now(), 'public_token' => 'tok12345678901234567890123456',
        ]);

        $r = $this->get(route('nps.show', 'tok12345678901234567890123456'));
        $r->assertOk();
        $r->assertSee('g.page', false);
    }

    #[Test] public function timer_start_stop_prefills_form(): void
    {
        $this->actingAs($this->user)->post(route('time-entries.timer-start'), ['note' => 'Koding'])
            ->assertSessionHas('success');
        $this->assertTrue(session()->has('timer_started_at'));

        $r = $this->actingAs($this->user)->post(route('time-entries.timer-stop'));
        $r->assertRedirect();
        $this->assertStringContainsString('prefill_hours', $r->headers->get('Location'));
        $this->assertFalse(session()->has('timer_started_at'));
    }
}
