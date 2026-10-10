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
 * Every primary GET page must render (200), never 500.
 * Catches Blade syntax/runtime errors like the settings regression.
 */
class RenderSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Workspace $ws;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPlans();
        $this->user = User::factory()->create();
        $this->ws = Workspace::factory()->create();
        $this->user->workspaces()->attach($this->ws->id, ['role' => 'admin']);
        $this->giveBusinessPlan($this->ws); // all feature flags on for full render
        session(['current_workspace_id' => $this->ws->id]);
        $client = Client::factory()->create(['workspace_id' => $this->ws->id]);
        Invoice::factory()->withTotal(100000)->create([
            'workspace_id' => $this->ws->id, 'client_id' => $client->id,
        ]);
    }

    #[Test] public function primary_pages_render(): void
    {
        $a = $this->actingAs($this->user);
        foreach ([
            'dashboard', 'clients.index', 'clients.create', 'products.index',
            'projects.index', 'invoices.index', 'invoices.create', 'quotes.index',
            'quotes.create', 'payments.index', 'recurring.index', 'reports.index',
            'reports.aging', 'reports.cashflow', 'reports.pl', 'reports.tax',
            'credit-notes.index', 'expenses.index', 'time-entries.index',
            'bank-reconcile.index', 'accounting-exports.index', 'exports.index',
            'coupons.index', 'gateways.index', 'team.index', 'workspace.settings',
            'billing.show', 'notifications.index', 'audit-logs.index',
            'api-tokens.index', 'tenant-webhooks.index', 'support.index',
            'affiliate.index', 'ai-providers.index', 'approvals.index',
            'approval-rules.index', 'email-forwards.index',
            'report-subscriptions.index', 'invoice-layouts.index',
            'dashboard.customize', 'profile.edit', 'clients.import.show',
        ] as $name) {
            $a->get(route($name))->assertOk("GET $name failed");
        }
        $this->assertTrue(true);
    }

    #[Test] public function public_pages_render(): void
    {
        foreach (['home', 'pricing', 'docs', 'blog.index', 'seo.templates', 'login', 'register'] as $name) {
            $params = $name === 'seo.templates' ? [] : [];
            $this->get(route($name, $params))->assertOk("GET $name failed");
        }
        $this->assertTrue(true);
    }
}
