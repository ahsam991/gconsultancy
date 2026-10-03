@extends('layouts.app')
@section('title', 'Blog Categories')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Blog Categories</h4>
    <a href="{{ Route::has('cms.blog.index') ? route('cms.blog.index') : url('/crm/cms/blog') }}" class="btn btn-sm btn-outline-secondary">Back to Posts</a>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>Add Category</h6>
    <form method="POST" action="{{ Route::has('cms.blog-categories.store') ? route('cms.blog-categories.store') : url('/crm/cms/blog-categories') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label small" for="cat_name">Name</label>
            <input type="text" name="name" id="cat_name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label small" for="cat_slug">Slug (optional)</label>
            <input type="text" name="slug" id="cat_slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror" maxlength="255">
            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="cat_active">Status</label>
            <select name="active" id="cat_active" class="form-select">
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary btn-sm">Add</button></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Name</th><th>Slug</th><th>Posts</th><th>Active</th><th></th></tr></thead>
<tbody>
@forelse(($categories ?? []) as $cat)
<tr>
    <td>{{ $cat->id }}</td>
    <td>{{ $cat->name }}</td>
    <td class="small">{{ $cat->slug }}</td>
    <td>{{ $cat->posts_count ?? 0 }}</td>
    <td>{{ $cat->active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <form method="POST" action="{{ Route::has('cms.blog-categories.update') ? route('cms.blog-categories.update', $cat) : url('/crm/cms/blog-categories/' . $cat->id) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $cat->name }}">
            <input type="hidden" name="active" value="{{ $cat->active ? 0 : 1 }}">
            <button class="btn btn-sm btn-outline-secondary">{{ $cat->active ? 'Hide' : 'Show' }}</button>
        </form>
        <form method="POST" action="{{ Route::has('cms.blog-categories.destroy') ? route('cms.blog-categories.destroy', $cat) : url('/crm/cms/blog-categories/' . $cat->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No categories yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($categories) && method_exists($categories, 'links')){{ $categories->links() }}@endif
</div></div>
@endsection
