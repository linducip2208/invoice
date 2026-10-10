<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperadminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@invocely.test'],
            [
                'name'              => 'Super Admin',
                'password'          => Hash::make('superadmin123'),
                'is_superadmin'     => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
