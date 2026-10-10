<?php
// E2E bootstrap seeder: plans + superadmin + one workspace user with data.
namespace Database\Seeders;

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class E2eSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([PlanSeeder::class, SuperadminSeeder::class]);

        $user = User::updateOrCreate(
            ['email' => 'e2e@invocely.test'],
            ['name' => 'E2E User', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );
        $ws = Workspace::firstOrCreate(
            ['slug' => 'e2e-workspace'],
            ['name' => 'E2E Workspace', 'currency' => 'IDR', 'invoice_prefix' => 'INV-']
        );
        if (! $user->workspaces()->where('workspace_id', $ws->id)->exists()) {
            $user->workspaces()->attach($ws->id, ['role' => 'owner']);
        }
        // Pro plan so E2E never trips Free limits (unlimited invoices/clients).
        $pro = Plan::where('slug', 'pro')->firstOrFail();
        Subscription::firstOrCreate(
            ['workspace_id' => $ws->id],
            ['plan_id' => $pro->id, 'status' => 'active', 'billing_period' => 'monthly', 'currency' => 'IDR']
        );
        Client::firstOrCreate(
            ['workspace_id' => $ws->id, 'email' => 'e2e-client@test.id'],
            ['name' => 'E2E Client', 'company_name' => 'E2E Corp']
        );

        // Portal designated approver (known password, verified, active).
        $client = Client::where('workspace_id', $ws->id)->first();
        \App\Models\ClientPortalUser::firstOrCreate(
            ['client_id' => $client->id, 'email' => 'e2e-approver@test.id'],
            [
                'workspace_id' => $ws->id,
                'password_hash' => Hash::make('password'),
                'email_verified_at' => now(),
                'can_approve_quotes' => true,
                'is_active' => true,
            ]
        );

        // Public quote fixture with a stable token for E2E.
        \App\Models\Quote::firstOrCreate(
            ['workspace_id' => $ws->id, 'quote_no' => 'Q-E2E-0001'],
            [
                'client_id' => $client->id,
                'status' => 'sent',
                'revision' => 1,
                'issue_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(30)->toDateString(),
                'currency' => 'IDR',
                'tax_rate' => 10,
                'subtotal' => 200000,
                'tax_amount' => 20000,
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total' => 220000,
                'items' => [['description' => 'E2E service', 'quantity' => 2, 'unit_price' => 100000, 'tax_rate' => 0]],
                'public_token' => 'e2e-quote-token-000000000001',
            ]
        );

        // Second fixture, never approved by any flow (outsider/CSRF probes).
        // Mobile project gets its own pair (0003/0004) so desktop and mobile
        // runs never share mutable quote state.
        \App\Models\Quote::firstOrCreate(
            ['workspace_id' => $ws->id, 'quote_no' => 'Q-E2E-0002'],
            [
                'client_id' => $client->id,
                'status' => 'sent',
                'revision' => 1,
                'issue_date' => now()->toDateString(),
                'expiry_date' => now()->addDays(30)->toDateString(),
                'currency' => 'IDR',
                'tax_rate' => 0,
                'subtotal' => 50000,
                'tax_amount' => 0,
                'discount_type' => 'none',
                'discount_value' => 0,
                'discount_amount' => 0,
                'total' => 50000,
                'items' => [['description' => 'E2E small service', 'quantity' => 1, 'unit_price' => 50000, 'tax_rate' => 0]],
                'public_token' => 'e2e-quote-token-000000000002',
            ]
        );

        foreach ([['Q-E2E-0003', 'e2e-quote-token-000000000003'], ['Q-E2E-0004', 'e2e-quote-token-000000000004']] as [$no, $token]) {
            \App\Models\Quote::firstOrCreate(
                ['workspace_id' => $ws->id, 'quote_no' => $no],
                [
                    'client_id' => $client->id,
                    'status' => 'sent',
                    'revision' => 1,
                    'issue_date' => now()->toDateString(),
                    'expiry_date' => now()->addDays(30)->toDateString(),
                    'currency' => 'IDR',
                    'tax_rate' => 0,
                    'subtotal' => 50000,
                    'tax_amount' => 0,
                    'discount_type' => 'none',
                    'discount_value' => 0,
                    'discount_amount' => 0,
                    'total' => 50000,
                    'items' => [['description' => 'E2E mobile service', 'quantity' => 1, 'unit_price' => 50000, 'tax_rate' => 0]],
                    'public_token' => $token,
                ]
            );
        }
    }
}
