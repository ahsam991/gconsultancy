@extends('layouts.app')
@section('title', 'Banners')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Banners</h4>
    <a href="{{ Route::has('cms.banners.create') ? route('cms.banners.create') : url('/crm/cms/banners/create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Banner</a>
</div>
<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Title</th><th>Location</th><th>Sort</th><th>Active</th><th></th></tr></thead>
<tbody>
@forelse(($banners ?? []) as $b)
<tr>
    <td>{{ $b->id }}</td>
    <td>{{ $b->title }}</td>
    <td>{{ $b->location }}</td>
    <td>{{ $b->sort_order }}</td>
    <td>{{ $b->active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <a href="{{ Route::has('cms.banners.edit') ? route('cms.banners.edit', $b) : url('/crm/cms/banners/' . $b->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit</a>
        <form method="POST" action="{{ Route::has('cms.banners.destroy') ? route('cms.banners.destroy', $b) : url('/crm/cms/banners/' . $b->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No banners yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($banners) && method_exists($banners, 'links')){{ $banners->links() }}@endif
</div></div>
@endsection
