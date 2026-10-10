<?php

namespace Database\Factories;

use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name) . '-' . fake()->unique()->randomNumber(3),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'city' => fake()->city(),
            'country' => 'Indonesia',
            'currency' => 'IDR',
            'invoice_prefix' => 'INV-',
            'default_template' => 'classic',
            'primary_color' => '#A78BFA',
            'accent_color' => '#FF7AC6',
        ];
    }
}
