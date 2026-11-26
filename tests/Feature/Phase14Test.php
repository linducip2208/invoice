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

class Phase14Test extends TestCase
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

    private function payload(int $productId, float $qty): array
    {
        return [
            'client_id' => $this->client->id,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(30)->toDateString(),
            'template' => 'classic', 'currency' => 'IDR',
            'discount_type' => 'none',
            'items' => [['product_id' => $productId, 'description' => 'Barang', 'quantity' => $qty, 'unit_price' => 10000]],
        ];
    }

    #[Test] public function stock_decrements_and_restocks(): void
    {
        $p = Product::factory()->create(['workspace_id' => $this->ws->id, 'track_stock' => true, 'stock_qty' => 10]);

        $this->actingAs($this->user)->post(route('invoices.store'), $this->payload($p->id, 3))->assertRedirect();
        $this->assertEquals(7, (float) $p->fresh()->stock_qty);

        $inv = Invoice::latest()->first();
        $this->actingAs($this->user)->put(route('invoices.update', $inv), $this->payload($p->id, 1))->assertRedirect();
        $this->assertEquals(9, (float) $p->fresh()->stock_qty);

        $this->actingAs($this->user)->delete(route('invoices.destroy', $inv))->assertRedirect();
        $this->assertEquals(10, (float) $p->fresh()->stock_qty);
    }

    #[Test] public function untracked_products_untouched(): void
    {
        $p = Product::factory()->create(['workspace_id' => $this->ws->id, 'track_stock' => false, 'stock_qty' => 5]);
        $this->actingAs($this->user)->post(route('invoices.store'), $this->payload($p->id, 3))->assertRedirect();
        $this->assertEquals(5, (float) $p->fresh()->stock_qty);
    }

    #[Test] public function dunning_queues_for_past_due_owners(): void
    {
        $pro = \App\Models\Plan::where('slug', 'pro')->first();
        $sub = $this->ws->activeSubscription();
        $sub->update(['plan_id' => $pro->id, 'status' => 'past_due', 'billing_period' => 'monthly', 'currency' => 'IDR']);

        $this->artisan('subscriptions:dun', ['--dry-run' => true])->assertSuccessful();
        $this->artisan('subscriptions:dun')->assertSuccessful();
        Mail::assertQueued(\App\Mail\SubscriptionDunningMail::class);
    }
}
