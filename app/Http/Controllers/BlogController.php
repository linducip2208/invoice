<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $posts = BlogPost::with('category')
            ->published()
            ->when($request->search, fn($q) => $q->where('title', 'like', "%{$request->search}%"))
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::whereHas('posts', fn($q) => $q->published())
            ->withCount(['posts' => fn($q) => $q->published()])
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'seoMeta' => [
                'title' => 'Blog',
                'description' => 'Tips, trik, dan panduan seputar invoicing, freelancing, dan manajemen bisnis UKM.',
            ],
        ]);
    }

    public function show(string $slug)
    {
        $post = BlogPost::with('category')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedPosts = BlogPost::with('category')
            ->published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('blog.show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'seoMeta' => [
                'title' => $post->meta_title,
                'description' => $post->meta_description,
                'og_type' => 'article',
                'published_time' => $post->published_at?->toIso8601String(),
            ],
        ]);
    }

    public function category(string $slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();

        $posts = BlogPost::with('category')
            ->published()
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(12);

        $categories = BlogCategory::whereHas('posts', fn($q) => $q->published())
            ->withCount(['posts' => fn($q) => $q->published()])
            ->get();

        return view('blog.index', [
            'posts' => $posts,
            'categories' => $categories,
            'currentCategory' => $category,
            'seoMeta' => [
                'title' => $category->name . ' — Blog',
                'description' => $category->description ?? 'Artikel tentang ' . $category->name,
            ],
        ]);
    }

    public function feed()
    {
        $posts = BlogPost::with('category')
            ->published()
            ->latest('published_at')
            ->take(50)
            ->get();

        return response()->view('blog.feed', ['posts' => $posts])
            ->header('Content-Type', 'application/xml; charset=utf-8');
    }
}
