@extends('layouts.app')
@section('title', 'Menus')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Menus</h4>
    <a href="{{ Route::has('cms.menus.create') ? route('cms.menus.create') : url('/crm/cms/menus/create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Menu</a>
</div>

@if(!empty($createMode) || isset($menu))
<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>{{ isset($menu) ? 'Edit Menu' : 'Add Menu' }}</h6>
    <form method="POST" action="{{ isset($menu) ? (Route::has('cms.menus.update') ? route('cms.menus.update', $menu) : url('/crm/cms/menus/' . $menu->id)) : (Route::has('cms.menus.store') ? route('cms.menus.store') : url('/crm/cms/menus')) }}">
        @csrf
        @if(isset($menu))@method('PUT')@endif
        @include('cms.menus._form', ['menu' => $menu ?? null])
        <div class="d-flex gap-2 mt-2">
            <button class="btn btn-primary btn-sm">Save</button>
            <a href="{{ Route::has('cms.menus.index') ? route('cms.menus.index') : url('/crm/cms/menus') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
        </div>
    </form>
</div></div>
@endif

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>#</th><th>Name</th><th>Location</th><th>Active</th><th>Items</th><th></th></tr></thead>
<tbody>
@forelse(($menus ?? []) as $m)
<tr>
    <td>{{ $m->id }}</td>
    <td>{{ $m->name }}</td>
    <td>{{ $m->location }}</td>
    <td>{{ $m->active ? 'Yes' : 'No' }}</td>
    <td>{{ $m->items_count ?? $m->items->count() ?? 0 }}</td>
    <td class="text-nowrap">
        <a href="{{ Route::has('cms.menus.items') ? route('cms.menus.items', $m) : url('/crm/cms/menus/' . $m->id . '/items') }}" class="btn btn-sm btn-outline-info">Items</a>
        <a href="{{ Route::has('cms.menus.edit') ? route('cms.menus.edit', $m) : url('/crm/cms/menus/' . $m->id . '/edit') }}" class="btn btn-sm btn-outline-warning">Edit</a>
        <form method="POST" action="{{ Route::has('cms.menus.destroy') ? route('cms.menus.destroy', $m) : url('/crm/cms/menus/' . $m->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No menus yet.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($menus) && method_exists($menus, 'links')){{ $menus->links() }}@endif
</div></div>
@endsection
