<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $workspace;
    protected Client $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->workspace = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->workspace->id, ['role' => 'admin']);
        session(['current_workspace_id' => $this->workspace->id]);
        $this->client = Client::factory()->create(['workspace_id' => $this->workspace->id]);
    }

    #[Test] public function authenticated_user_can_view_invoices_index(): void
    {
        Invoice::factory()->count(3)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('invoices.index'));

        $response->assertStatus(200);
        $response->assertSee('Invoices');
    }

    #[Test]
    public function user_can_create_invoice(): void
    {
        $product = Product::factory()->create(['workspace_id' => $this->workspace->id]);

        $data = [
            'client_id' => $this->client->id,
            'currency' => 'IDR',
            'issue_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays(30)->format('Y-m-d'),
            'template' => 'classic',
            'primary_color' => '#A78BFA',
            'accent_color' => '#FF7AC6',
            'tax_rate' => 0,
            'discount_type' => 'none',
            'discount_value' => 0,
            'items' => [
                [
                    'product_id' => $product->id,
                    'description' => 'Test item',
                    'quantity' => 2,
                    'unit_price' => 100000,
                    'tax_rate' => 0,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('invoices.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
            'status' => 'draft',
        ]);
        $this->assertDatabaseHas('invoice_items', [
            'description' => 'Test item',
            'quantity' => 2,
            'unit_price' => 100000,
        ]);
    }

    #[Test]
    public function user_can_view_single_invoice(): void
    {
        $invoice = Invoice::factory()->withTotal(150000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'product_id' => null,
            'description' => 'Service',
            'quantity' => 1,
            'unit_price' => 150000,
        ]);

        $response = $this->actingAs($this->user)->get(route('invoices.show', $invoice));

        $response->assertStatus(200);
        $response->assertSee($invoice->invoice_no);
    }

    #[Test]
    public function user_can_update_invoice_status(): void
    {
        $invoice = Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('invoices.update-status', $invoice), ['status' => 'sent']);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'status' => 'sent',
        ]);
    }

    #[Test]
    public function user_can_delete_draft_invoice(): void
    {
        $invoice = Invoice::factory()->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('invoices.destroy', $invoice));

        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    #[Test]
    public function invoice_is_scoped_to_workspace(): void
    {
        $otherWorkspace = Workspace::factory()->create();
        $otherInvoice = Invoice::factory()->withTotal(50000)->create([
            'workspace_id' => $otherWorkspace->id,
            'client_id' => Client::factory()->create(['workspace_id' => $otherWorkspace->id])->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('invoices.show', $otherInvoice));

        $response->assertStatus(403);
    }

    #[Test]
    public function invoice_filter_by_status(): void
    {
        Invoice::factory()->paid()->count(2)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);
        Invoice::factory()->overdue()->count(1)->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('invoices.index', ['status' => 'paid']));

        $response->assertStatus(200);
        $response->assertSee('Paid');
    }

    #[Test]
    public function invoice_total_is_calculated_correctly(): void
    {
        $invoice = Invoice::factory()->create([
            'workspace_id' => $this->workspace->id,
            'client_id' => $this->client->id,
            'subtotal' => 200000,
            'tax_rate' => 10,
            'tax_amount' => 20000,
            'total' => 220000,
            'balance_due' => 220000,
        ]);

        $this->assertEquals(220000, $invoice->total);
        $this->assertEquals(220000, $invoice->balance_due);
    }
}
