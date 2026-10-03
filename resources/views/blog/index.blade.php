@extends('public.layout')
@section('title', 'Blog | Global Consultancy')
@section('meta_description', 'Guides and updates on studying abroad — destinations, admissions, visas and scholarships.')
@section('content')
<div class="container py-5">
    <h1>Blog</h1>
    <div class="mb-3 d-flex flex-wrap gap-2">
        <a href="{{ Route::has('blog.index') ? route('blog.index') : url('/blog') }}" class="btn btn-sm {{ empty($activeCategory) ? 'btn-primary' : 'btn-outline-secondary' }}">All</a>
        @foreach(($categories ?? []) as $cat)
            <a href="{{ (Route::has('blog.index') ? route('blog.index', ['category' => $cat->slug]) : url('/blog?category=' . $cat->slug)) }}" class="btn btn-sm {{ ($activeCategory ?? '') == $cat->slug ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $cat->name }}</a>
        @endforeach
    </div>
    <div class="row g-3">
        @forelse(($posts ?? []) as $post)
            <div class="col-12 col-md-6 col-lg-4">
                <article class="card h-100">
                    @if($post->featured_image)
                        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="card-img-top" style="height:180px;object-fit:cover">
                    @endif
                    <div class="card-body">
                        <p class="small text-muted mb-1">{{ $post->category->name ?? 'General' }} · {{ $post->published_at?->format('M d, Y') }}</p>
                        <h2 class="h5">{{ $post->title }}</h2>
                        <p class="small text-muted">{{ $post->excerpt }}</p>
                        <a class="btn btn-sm btn-outline-primary" href="{{ Route::has('blog.show') ? route('blog.show', $post->slug) : url('/blog/' . $post->slug) }}">Read more</a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No posts published yet. Please check back soon.</p></div>
        @endforelse
    </div>
    @if(isset($posts) && method_exists($posts, 'links'))
        <div class="mt-4">{{ $posts->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
