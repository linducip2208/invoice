<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Dashboard KPIs against a hand-computed dataset.
 * Dataset: paid 100k | sent bal 50k | overdue bal 25k | draft 999k (ignored).
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function kpis_match_hand_computed_values(): void
    {
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);

        $mk = fn (string $status, float $total, float $paid) => Invoice::factory()->create([
            'workspace_id' => $ws->id, 'client_id' => $client->id, 'status' => $status,
            'subtotal' => $total, 'tax_rate' => 0, 'tax_amount' => 0,
            'total' => $total, 'amount_paid' => $paid, 'balance_due' => $total - $paid,
            'due_date' => $status === 'overdue' ? now()->subDay() : now()->addMonth(),
        ]);
        $mk('paid', 100000, 100000);
        $mk('sent', 50000, 0);
        $mk('overdue', 25000, 0);
        $mk('draft', 999000, 0);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertOk();
        // Exact KPI values from the controller (draft 999k must be excluded).
        $response->assertViewHas('totalRevenue', 100000.0);
        $response->assertViewHas('outstandingAmount', 75000.0);
        $response->assertViewHas('paidCount', 1);
        $response->assertViewHas('overdueCount', 1);
        $response->assertViewHas('clientCount', 1);
    }
}
