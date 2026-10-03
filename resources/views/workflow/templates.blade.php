@extends('layouts.app')
@section('title','Workflow Templates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Workflow Templates</h4>
    <div class="d-flex gap-2">
        @if(Route::has('workflow.transitions'))<a href="{{ route('workflow.transitions') }}" class="btn btn-sm btn-outline-secondary">Transitions</a>@endif
        @if(Route::has('workflow.fields'))<a href="{{ route('workflow.fields') }}" class="btn btn-sm btn-outline-secondary">Custom Fields</a>@endif
    </div>
</div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">New Template</div><div class="card-body">
@if(Route::has('workflow.templates.store'))
<form method="POST" action="{{ route('workflow.templates.store') }}" class="row g-2">@csrf
    <div class="col-md-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" class="form-control" required maxlength="255"></div>
    <div class="col-md-5"><label class="form-label">Description</label><input name="description" class="form-control"></div>
    <div class="col-md-2"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Create</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="templatesTable">
<thead><tr><th>Name</th><th>Description</th><th>Transitions</th><th>Active</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($templates ?? []) as $t)
<tr>
    <td>{{ $t->name }}</td><td>{{ $t->description ?? '—' }}</td><td class="tnum">{{ $t->transitions_count ?? 0 }}</td>
    <td><x-status-badge :status="$t->active ? 'active' : 'inactive'"/></td>
    <td class="text-nowrap">
        @if(Route::has('workflow.templates.update'))
        <form method="POST" action="{{ route('workflow.templates.update', $t) }}" class="d-inline">@csrf @method('PATCH')
            <input type="hidden" name="name" value="{{ $t->name }}"><input type="hidden" name="description" value="{{ $t->description }}">
            <select name="active" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()"><option value="1" @selected($t->active)>Active</option><option value="0" @selected(!$t->active)>Inactive</option></select>
        </form>
        @endif
        @if(Route::has('workflow.templates.destroy'))
        <form method="POST" action="{{ route('workflow.templates.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Delete template?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
        @endif
    </td>
</tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($templates ?? null, 'links'))<div class="mt-3">{{ $templates->links() }}</div>@endif
</div></div>
@endsection
