<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Quote;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase12Test extends TestCase
{
    use RefreshDatabase;

    #[Test] public function quotes_and_products_csv_export(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);
        Quote::create([
            'workspace_id' => $ws->id, 'client_id' => $client->id,
            'quote_no' => 'Q-CSV-1', 'status' => 'sent', 'revision' => 1,
            'issue_date' => now(), 'currency' => 'IDR', 'tax_rate' => 0,
            'subtotal' => 100, 'tax_amount' => 0,
            'discount_type' => 'none', 'discount_value' => 0, 'discount_amount' => 0,
            'total' => 100, 'items' => [],
        ]);
        Product::factory()->create(['workspace_id' => $ws->id, 'name' => 'Kertas A4']);

        $q = $this->actingAs($user)->get(route('exports.quotes'));
        $q->assertOk();
        $this->assertStringContainsString('text/csv', $q->headers->get('Content-Type'));
        $this->assertStringContainsString('Q-CSV-1', $q->streamedContent());

        $p = $this->actingAs($user)->get(route('exports.products'));
        $p->assertOk();
        $this->assertStringContainsString('Kertas A4', $p->streamedContent());
    }
}
