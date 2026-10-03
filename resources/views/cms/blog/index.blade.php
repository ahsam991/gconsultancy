@extends('layouts.app')
@section('title', 'Blog Posts')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Blog Posts</h4>
    <div class="d-flex gap-2">
        <a href="{{ Route::has('cms.blog-categories.index') ? route('cms.blog-categories.index') : url('/crm/cms/blog-categories') }}" class="btn btn-sm btn-outline-secondary">Categories</a>
        <a href="{{ Route::has('cms.blog.create') ? route('cms.blog.create') : url('/crm/cms/blog/create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Post</a>
    </div>
</div>
<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Title</th><th>Category</th><th>Status</th><th>Published</th><th></th></tr></thead>
<tbody>
@forelse(($posts ?? []) as $p)
<tr>
    <td>{{ $p->id }}</td>
    <td>{{ $p->title }}</td>
    <td>{{ $p->category->name ?? '—' }}</td>
    <td>{{ $p->status }}</td>
    <td class="small">{{ $p->published_at?->format('Y-m-d H:i') ?? '—' }}</td>
    <td class="text-nowrap">
        <a href="{{ Route::has('cms.blog.edit') ? route('cms.blog.edit', $p) : url('/crm/cms/blog/' . $p->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit</a>
        <form method="POST" action="{{ Route::has('cms.blog.destroy') ? route('cms.blog.destroy', $p) : url('/crm/cms/blog/' . $p->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No posts yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($posts) && method_exists($posts, 'links')){{ $posts->links() }}@endif
</div></div>
@endsection
