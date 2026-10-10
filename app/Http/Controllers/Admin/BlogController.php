<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\Seo\IndexNowService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('category')
            ->latest()
            ->paginate(20);

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.create', compact('categories'));
    }

    public function store(Request $request, IndexNowService $indexNow)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['author_id'] = auth()->id();

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $post = BlogPost::create($data);

        if ($post->is_published) {
            $indexNow->submitSingle('/blog/' . $post->slug);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(BlogPost $post)
    {
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.blog.edit', compact('post', 'categories'));
    }

    public function update(Request $request, BlogPost $post, IndexNowService $indexNow)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:blog_categories,id',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:500',
            'is_published' => 'boolean',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:300',
        ]);

        if ($data['title'] !== $post->title) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (!empty($data['is_published']) && empty($data['published_at'])) {
            $data['published_at'] = $data['published_at'] ?? now();
        }

        $wasPublished = $post->is_published;
        $post->update($data);

        if ($post->is_published) {
            $indexNow->submitSingle('/blog/' . $post->slug);
        }

        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(BlogPost $post)
    {
        $post->delete();
        return redirect()->route('admin.blog.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }

    // Categories
    public function categories()
    {
        $categories = BlogCategory::orderBy('name')->paginate(20);
        return view('admin.blog.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        $data['slug'] = Str::slug($data['name']);

        BlogCategory::create($data);
        return redirect()->route('admin.blog.categories')
            ->with('success', 'Kategori berhasil dibuat.');
    }

    public function updateCategory(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
        $data['slug'] = Str::slug($data['name']);

        $category->update($data);
        return redirect()->route('admin.blog.categories')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyCategory(BlogCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.blog.categories')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
