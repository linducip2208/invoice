<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase10Test extends TestCase
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

    private function makeBundle(?float $price = 900000): ProductBundle
    {
        $a = Product::factory()->create(['workspace_id' => $this->ws->id, 'unit_price' => 500000, 'is_active' => true]);
        $b = Product::factory()->create(['workspace_id' => $this->ws->id, 'unit_price' => 600000, 'is_active' => true]);
        $bundle = ProductBundle::create(['workspace_id' => $this->ws->id, 'name' => 'Paket X', 'price' => $price, 'is_active' => true]);
        $bundle->items()->create(['product_id' => $a->id, 'quantity' => 1]);
        $bundle->items()->create(['product_id' => $b->id, 'quantity' => 2]);
        return $bundle->fresh();
    }

    #[Test] public function bundle_crud_and_fixed_price_invoice(): void
    {
        $r = $this->actingAs($this->user)->post(route('bundles.store'), [
            'name' => 'Paket Y', 'price' => 1000000, 'is_active' => 1,
            'items' => [['product_id' => Product::factory()->create(['workspace_id' => $this->ws->id])->id, 'quantity' => 1]],
        ]);
        $r->assertRedirect();
        $this->assertDatabaseHas('product_bundles', ['name' => 'Paket Y']);

        $bundle = $this->makeBundle(900000);
        $r2 = $this->actingAs($this->user)->post(route('bundles.invoice', $bundle), ['client_id' => $this->client->id]);
        $r2->assertRedirect();
        $inv = \App\Models\Invoice::latest()->first();
        $this->assertEquals(900000, (float) $inv->total);
        $this->assertEquals(1, $inv->items()->count());
    }

    #[Test] public function bundle_without_price_expands_components(): void
    {
        $bundle = $this->makeBundle(null);
        $this->actingAs($this->user)->post(route('bundles.invoice', $bundle), ['client_id' => $this->client->id])->assertRedirect();
        $inv = \App\Models\Invoice::latest()->first();
        // 500rb×1 + 600rb×2 = 1.700.000
        $this->assertEquals(1700000, (float) $inv->total);
        $this->assertEquals(2, $inv->items()->count());
    }

    #[Test] public function bundle_isolated_per_workspace(): void
    {
        $bundle = $this->makeBundle();
        $other = User::factory()->create();
        $ws2 = Workspace::factory()->create();
        $other->workspaces()->attach($ws2->id, ['role' => 'admin']);
        session(['current_workspace_id' => $ws2->id]);

        $this->actingAs($other)->get(route('bundles.show', $bundle->id))->assertNotFound();
        $this->actingAs($other)->get(route('bundles.index'))->assertOk();
    }
}
