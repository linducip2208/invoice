<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase8Test extends TestCase
{
    use RefreshDatabase;

    #[Test] public function bulk_mark_sent_only_draft_or_cancelled(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);

        $draft = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $ws->id, 'client_id' => $client->id, 'status' => 'draft']);
        $paid = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $ws->id, 'client_id' => $client->id, 'status' => 'paid']);

        $this->actingAs($user)->post(route('invoices.bulk-mark-sent'), ['ids' => [$draft->id, $paid->id]])
            ->assertSessionHas('success');

        $this->assertEquals('sent', $draft->fresh()->status);
        $this->assertNotNull($draft->fresh()->last_sent_at);
        $this->assertEquals('paid', $paid->fresh()->status);
    }
}
