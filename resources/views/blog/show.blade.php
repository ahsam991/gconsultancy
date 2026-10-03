@extends('public.layout')
@section('title', ($post->meta_title ?? $post->title) . ' | Global Consultancy')
@section('meta_description', $post->meta_description ?? $post->excerpt ?? '')
@section('content')
<article class="container py-5" style="max-width:760px">
    <p class="small text-muted mb-1">
        <a href="{{ Route::has('blog.index') ? route('blog.index') : url('/blog') }}">Blog</a>
        @if($post->category)
            · <a href="{{ Route::has('blog.index') ? route('blog.index', ['category' => $post->category->slug]) : url('/blog?category=' . $post->category->slug) }}">{{ $post->category->name }}</a>
        @endif
    </p>
    <h1>{{ $post->title }}</h1>
    <p class="text-muted small">{{ $post->published_at?->format('F d, Y') }}</p>
    @if($post->featured_image)
        <img src="{{ asset('storage/' . $post->featured_image) }}" alt="{{ $post->title }}" class="img-fluid rounded mb-4">
    @endif
    @if($post->excerpt)
        <p class="lead">{{ $post->excerpt }}</p>
    @endif
    <div>{!! nl2br(e($post->content)) !!}</div>

    @if(isset($related) && count($related))
        <hr class="my-5">
        <h2 class="h5">Related posts</h2>
        <div class="row g-3">
            @foreach($related as $rel)
                <div class="col-12 col-md-4">
                    <div class="card h-100"><div class="card-body">
                        <h3 class="h6">{{ $rel->title }}</h3>
                        <a class="btn btn-sm btn-outline-primary" href="{{ Route::has('blog.show') ? route('blog.show', $rel->slug) : url('/blog/' . $rel->slug) }}">Read</a>
                    </div></div>
                </div>
            @endforeach
        </div>
    @endif
</article>
@endsection
