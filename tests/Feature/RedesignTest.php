<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Client;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RedesignTest extends TestCase
{
    use RefreshDatabase;

    #[Test] public function landing_renders_with_real_cta_and_pricing(): void
    {
        $this->seedPlans();
        $r = $this->get(route('home'));
        $r->assertOk();
        $r->assertSee('Invoice Profesional. Pembayaran Lebih Cepat.', false);
        $r->assertSee('Mulai Gratis', false);
        $r->assertSee('Lihat Demo', false);
        $r->assertSee('Kelola Invoice Bisnis Lebih Mudah Mulai Hari Ini.', false);
        $r->assertSee('Free', false);
        $r->assertSee('Pro', false);
        $r->assertSee('Business', false);
    }

    #[Test] public function registration_cta_links_exist(): void
    {
        $this->seedPlans();
        $r = $this->get(route('home'));
        $r->assertSee(route('register'), false);
    }

    #[Test] public function pricing_matches_database(): void
    {
        $this->seedPlans();
        $r = $this->get(route('pricing'));
        $r->assertOk();
        foreach (\App\Models\Plan::where('is_public', true)->get() as $plan) {
            $r->assertSee($plan->name, false);
        }
    }

    #[Test] public function superadmin_dashboard_kpi_and_sections(): void
    {
        $this->seedPlans();
        $admin = User::factory()->create(['is_superadmin' => true]);
        $r = $this->actingAs($admin)->get(route('admin.dashboard'));
        $r->assertOk();
        $r->assertSee('Total Users', false);
        $r->assertSee('MRR This Month (IDR)', false);
        $r->assertSee('Subscription management', false);
        $r->assertSee('Payment monitoring', false);
        $r->assertSee('Activity center', false);
        $r->assertSee('Quick actions', false);
    }

    #[Test] public function superadmin_new_pages_render(): void
    {
        $this->seedPlans();
        $admin = User::factory()->create(['is_superadmin' => true]);
        foreach (['admin.analytics', 'admin.revenue', 'admin.settings', 'admin.audit-logs', 'admin.subscriptions.index', 'admin.users.index', 'admin.workspaces.index'] as $name) {
            $this->actingAs($admin)->get(route($name))->assertOk("GET $name failed");
        }
    }

    #[Test] public function workspace_dashboard_has_new_widgets(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $r = $this->actingAs($user)->get(route('dashboard'));
        $r->assertOk();
        $r->assertSee('Collection rate', false);
        $r->assertSee('Total customers', false);
        $r->assertSee('Top customers', false);
        $r->assertSee('Upcoming due dates', false);
        $r->assertSee('Recent payments', false);
    }

    #[Test] public function invoice_bulk_and_filtering_works(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);
        $inv = Invoice::factory()->withTotal(100000)->create(['workspace_id' => $ws->id, 'client_id' => $client->id, 'status' => 'draft']);

        $this->actingAs($user)->get(route('invoices.index', ['client_id' => $client->id, 'sort' => 'total', 'order' => 'asc']))->assertOk();
        $this->actingAs($user)->post(route('invoices.bulk-destroy'), ['ids' => [$inv->id]])->assertRedirect();
        $this->assertDatabaseMissing('invoices', ['id' => $inv->id]);
    }

    #[Test] public function bilingual_template_accepted(): void
    {
        $this->seedPlans();
        $user = User::factory()->create();
        $ws = Workspace::factory()->create();
        $user->workspaces()->attach($ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($ws);
        session(['current_workspace_id' => $ws->id]);
        $client = Client::factory()->create(['workspace_id' => $ws->id]);
        $payload = [
            'client_id' => $client->id, 'issue_date' => now()->toDateString(), 'due_date' => now()->addDays(30)->toDateString(),
            'template' => 'bilingual', 'currency' => 'IDR', 'discount_type' => 'none',
            'items' => [['description' => 'Jasa', 'quantity' => 1, 'unit_price' => 100000]],
        ];
        $this->actingAs($user)->post(route('invoices.store'), $payload)->assertRedirect();
        $this->assertDatabaseHas('invoices', ['template' => 'bilingual']);
    }

    #[Test] public function navigation_has_no_broken_links(): void
    {
        $this->seedPlans();
        $this->get(route('privacy'))->assertOk();
        $this->get(route('terms'))->assertOk();
    }
}
