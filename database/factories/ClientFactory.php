<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'workspace_id' => Workspace::factory(),
            'name' => fake()->name(),
            'company_name' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => 'Indonesia',
            'postal_code' => fake()->postcode(),
            'tax_id' => null,
            'notes' => null,
            'is_active' => true,
        ];
    }
}
