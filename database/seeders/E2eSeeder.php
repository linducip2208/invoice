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
    }
}
