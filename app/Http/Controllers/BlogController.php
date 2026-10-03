<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected function publishedQuery()
    {
        return BlogPost::with('category')
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function index(Request $request)
    {
        $request->validate([
            'category' => 'nullable|string|max:255',
        ]);

        $query = $this->publishedQuery()->orderByDesc('published_at')->orderByDesc('created_at');

        if ($request->filled('category')) {
            $slug = $request->input('category');
            $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
        }

        $posts = $query->paginate(12)->withQueryString();
        $categories = BlogCategory::where('active', true)->orderBy('name')->get();
        $activeCategory = $request->input('category');

        return view('blog.index', compact('posts', 'categories', 'activeCategory'));
    }

    public function show($slug)
    {
        $post = BlogPost::with('category')->where('slug', $slug)->first();

        if (! $post || $post->status !== 'published') {
            abort(404);
        }

        if ($post->published_at && $post->published_at->isFuture()) {
            abort(404);
        }

        $related = $this->publishedQuery()
            ->where('id', '!=', $post->id)
            ->when($post->blog_category_id, fn ($q) => $q->where('blog_category_id', $post->blog_category_id))
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'related'));
    }
}
