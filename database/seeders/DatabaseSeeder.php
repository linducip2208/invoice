<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            InvoiceTemplateSeeder::class,
        ]);

        // Demo/superadmin accounts use PUBLIC credentials and must NEVER be
        // seeded into production by accident. They run in local/dev/testing
        // or when explicitly opted in via SEED_DEMO=true.
        $seedDemo = filter_var(env('SEED_DEMO', false), FILTER_VALIDATE_BOOLEAN);

        if ($seedDemo || app()->environment('local', 'development', 'testing')) {
            $this->call([
                SuperadminSeeder::class,
                DemoDataSeeder::class,
            ]);
        } else {
            $this->command->warn('Skipping SuperadminSeeder + DemoDataSeeder (production safety). Set SEED_DEMO=true to include demo accounts.');
        }
    }
}
