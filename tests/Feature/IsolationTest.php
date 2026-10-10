<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Quote;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Cross-workspace negative tests across all major resources.
 * Every access must be 403 (explicit guard) or 404 (scoped binding) — never 200.
 */
class IsolationTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected Workspace $wsA;
    protected User $userB;
    protected Workspace $wsB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->userA = User::factory()->create();
        $this->wsA = Workspace::factory()->create();
        $this->userA->workspaces()->attach($this->wsA->id, ['role' => 'admin']);
        $this->giveProPlan($this->wsA);
        $this->userB = User::factory()->create();
        $this->wsB = Workspace::factory()->create();
        $this->userB->workspaces()->attach($this->wsB->id, ['role' => 'admin']);
        $this->giveProPlan($this->wsB);
        session(['current_workspace_id' => $this->wsA->id]);
    }

    protected function assertDenied($response): void
    {
        $this->assertContains($response->status(), [403, 404],
            'Cross-workspace access must be denied, got ' . $response->status());
    }

    protected function seedB(): array
    {
        $client = Client::factory()->create(['workspace_id' => $this->wsB->id]);
        $product = Product::factory()->create(['workspace_id' => $this->wsB->id]);
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->wsB->id, 'client_id' => $client->id,
        ]);
        $payment = Payment::factory()->create([
            'workspace_id' => $this->wsB->id, 'invoice_id' => $invoice->id, 'amount' => 10000,
        ]);
        $quote = Quote::create([
            'workspace_id' => $this->wsB->id, 'client_id' => $client->id,
            'quote_no' => 'Q-TEST-B-' . random_int(1000, 9999) . '-' . microtime(true), 'status' => 'draft',
            'issue_date' => now()->toDateString(), 'currency' => 'IDR',
            'tax_rate' => 0, 'subtotal' => 50000, 'tax_amount' => 0, 'total' => 50000,
            'items' => [['description' => 'x', 'quantity' => 1, 'unit_price' => 50000]],
        ]);
        return compact('client', 'product', 'invoice', 'payment', 'quote');
    }

    #[Test]
    public function cannot_read_other_workspace_resources(): void
    {
        ['client' => $c, 'invoice' => $i, 'payment' => $p, 'quote' => $q] = $this->seedB();
        $asA = fn ($url) => $this->actingAs($this->userA)->get($url);

        $this->assertDenied($asA(route('clients.show', $c)));
        $this->assertDenied($asA(route('clients.edit', $c)));
        $this->assertDenied($asA(route('invoices.show', $i)));
        $this->assertDenied($asA(route('invoices.edit', $i)));
        $this->assertDenied($asA(route('quotes.show', $q)));
        $this->assertDenied($asA(route('products.edit', $this->seedB()['product'])));
    }

    #[Test]
    public function cannot_write_other_workspace_resources(): void
    {
        ['client' => $c, 'invoice' => $i, 'payment' => $p, 'quote' => $q] = $this->seedB();
        $a = $this->actingAs($this->userA);

        $this->assertDenied($a->put(route('clients.update', $c), ['name' => 'Hijack', 'is_active' => '1']));
        $this->assertDenied($a->delete(route('clients.destroy', $c)));
        $this->assertDenied($a->post(route('invoices.update-status', $i), ['status' => 'sent']));
        $this->assertDenied($a->delete(route('payments.destroy', $p)));
        $this->assertDenied($a->delete(route('quotes.destroy', $q)));

        // Victim rows unchanged.
        $this->assertDatabaseHas('clients', ['id' => $c->id, 'name' => $c->name]);
        $this->assertDatabaseHas('invoices', ['id' => $i->id, 'status' => $i->status]);
        $this->assertDatabaseHas('payments', ['id' => $p->id]);
    }

    #[Test]
    public function cannot_attach_foreign_client_or_product_to_invoice(): void
    {
        ['client' => $foreignClient, 'product' => $foreignProduct] = $this->seedB();

        $response = $this->actingAs($this->userA)->post(route('invoices.store'), [
            'client_id' => $foreignClient->id,
            'currency' => 'IDR',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'template' => 'classic',
            'discount_type' => 'none',
            'discount_value' => 0,
            'items' => [[
                'product_id' => $foreignProduct->id,
                'description' => 'Hijacked',
                'quantity' => 1,
                'unit_price' => 1000,
                'tax_rate' => 0,
            ]],
        ]);

        $response->assertSessionHasErrors(['client_id', 'items.0.product_id']);
        $this->assertDatabaseMissing('invoice_items', ['description' => 'Hijacked']);
    }

    #[Test]
    public function listings_never_include_other_workspace_rows(): void
    {
        $seeded = $this->seedB();
        Client::factory()->create(['workspace_id' => $this->wsA->id, 'name' => 'Own Client XYZ']);

        $response = $this->actingAs($this->userA)->get(route('clients.index'));
        $response->assertOk();
        $response->assertSee('Own Client XYZ');
        $response->assertDontSee($seeded['client']->name);
    }
}
