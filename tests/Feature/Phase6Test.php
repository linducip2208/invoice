<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase6Test extends TestCase
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

    #[Test] public function bulk_pdf_returns_zip(): void
    {
        $a = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id]);
        $b = Invoice::factory()->withTotal(200000)->create(['workspace_id' => $this->ws->id, 'client_id' => $this->client->id]);

        $r = $this->actingAs($this->user)->post(route('invoices.bulk-pdf'), ['ids' => [$a->id, $b->id]]);
        $r->assertOk();
        $this->assertStringContainsString('zip', $r->headers->get('Content-Type'));
    }

    #[Test] public function client_statement_renders_with_totals(): void
    {
        Invoice::factory()->withTotal(500000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $this->client->id,
            'status' => 'partial', 'amount_paid' => 200000, 'balance_due' => 300000,
        ]);

        $r = $this->actingAs($this->user)->get(route('clients.statement', $this->client));
        $r->assertOk();
        $r->assertSee('Mutasi', false);
        $r->assertSee('300,000', false);
    }

    #[Test] public function product_duplicate_creates_inactive_copy(): void
    {
        $p = Product::factory()->create(['workspace_id' => $this->ws->id, 'name' => 'Jasa A']);

        $r = $this->actingAs($this->user)->post(route('products.duplicate', $p));
        $r->assertRedirect();

        $copy = Product::where('name', 'Jasa A (copy)')->first();
        $this->assertNotNull($copy);
        $this->assertFalse((bool) $copy->is_active);
    }
}
