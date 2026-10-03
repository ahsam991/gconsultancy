@extends('layouts.app')
@section('title', 'Edit Blog Post')
@section('content')
<h4 class="mb-3">Edit Blog Post</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.blog.update') ? route('cms.blog.update', $post) : url('/crm/cms/blog/' . $post->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('cms.blog._form', ['post' => $post, 'categories' => $categories ?? []])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ Route::has('cms.blog.index') ? route('cms.blog.index') : url('/crm/cms/blog') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
