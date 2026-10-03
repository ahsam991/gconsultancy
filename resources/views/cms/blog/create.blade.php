@extends('layouts.app')
@section('title', 'Add Blog Post')
@section('content')
<h4 class="mb-3">Add Blog Post</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.blog.store') ? route('cms.blog.store') : url('/crm/cms/blog') }}" enctype="multipart/form-data">
    @csrf
    @include('cms.blog._form', ['post' => null, 'categories' => $categories ?? []])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ Route::has('cms.blog.index') ? route('cms.blog.index') : url('/crm/cms/blog') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
