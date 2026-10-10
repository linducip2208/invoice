<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BlogTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function public_can_view_blog_index(): void
    {
        $category = BlogCategory::factory()->create();
        BlogPost::factory()->published()->count(5)->create(['category_id' => $category->id]);

        $response = $this->get(route('blog.index'));

        $response->assertStatus(200);
        $response->assertSee('Blog');
    }

    #[Test]
    public function public_can_view_single_post(): void
    {
        $post = BlogPost::factory()->published()->create();

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(200);
        $response->assertSee($post->title);
    }

    #[Test]
    public function draft_post_is_not_visible(): void
    {
        $post = BlogPost::factory()->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $response = $this->get(route('blog.show', $post->slug));

        $response->assertStatus(404);
    }

    #[Test]
    public function blog_has_rss_feed(): void
    {
        BlogPost::factory()->published()->count(3)->create();

        $response = $this->get(route('blog.feed'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/xml; charset=utf-8');
    }

    #[Test]
    public function user_can_create_blog_post(): void
    {
        $data = [
            'title' => 'Test Blog Post',
            'content' => '<p>Test content</p>',
            'is_published' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.blog.store'), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('blog_posts', [
            'title' => 'Test Blog Post',
            'slug' => 'test-blog-post',
            'is_published' => true,
        ]);
    }

    #[Test]
    public function blog_post_requires_content(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.blog.store'), [
            'title' => 'No Content',
        ]);

        $response->assertSessionHasErrors('content');
    }

    #[Test]
    public function user_can_create_category(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.blog.categories.store'), [
            'name' => 'Test Category',
            'description' => 'A test category',
        ]);

        $this->assertDatabaseHas('blog_categories', [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
    }
}
