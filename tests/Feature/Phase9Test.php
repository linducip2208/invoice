<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class Phase9Test extends TestCase
{
    use RefreshDatabase;

    #[Test] public function payment_receipt_renders_and_isolated(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);
        $inv = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $ws->id, 'client_id' => $client->id]);

        $pay = Payment::create([
            'workspace_id' => $ws->id, 'invoice_id' => $inv->id,
            'amount' => 100000, 'payment_date' => now(), 'payment_method' => 'bank_transfer',
        ]);

        $r = $this->actingAs($user)->get(route('payments.receipt', $pay));
        $r->assertOk();
        $r->assertSee('KWITANSI', false);
        $r->assertSee($client->name, false);

        // Workspace lain → tidak terlihat (404 via scope; 403 bila scope nonaktif).
        $other = User::factory()->create();
        $ws2 = Workspace::factory()->create();
        $other->workspaces()->attach($ws2->id, ['role' => 'admin']);
        session(['current_workspace_id' => $ws2->id]);
        $this->actingAs($other)->get(route('payments.receipt', $pay))->assertNotFound();
    }

    #[Test] public function ti_icon_always_sized(): void
    {
        $html = \Illuminate\Support\Facades\Blade::render('<x-ti name="check" class="text-success" />');
        $this->assertStringContainsString('icon', $html);
        $this->assertStringContainsString('width="24"', $html);
        $this->assertStringContainsString('aria-hidden', $html);
    }
}
