<?php

namespace Database\Factories;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BlogPostFactory extends Factory
{
    protected $model = BlogPost::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        return [
            'category_id' => BlogCategory::factory(),
            'author_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->sentence(15),
            'content' => '<p>' . fake()->paragraphs(3, true) . '</p>',
            'featured_image' => null,
            'is_published' => true,
            'published_at' => now()->subDays(rand(1, 30)),
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn() => [
            'is_published' => true,
            'published_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn() => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }
}
