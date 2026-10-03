@extends('layouts.app')
@section('title','Custom Fields')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Custom Fields</h4>
    @if(Route::has('workflow.templates'))<a href="{{ route('workflow.templates') }}" class="btn btn-sm btn-outline-secondary">Templates</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">New Custom Field</div><div class="card-body">
@if(Route::has('workflow.fields.store'))
<form method="POST" action="{{ route('workflow.fields.store') }}" class="row g-2">@csrf
    <div class="col-md-2"><label class="form-label">Module <span class="text-danger">*</span></label><input name="module" class="form-control" required maxlength="100" placeholder="candidate"></div>
    <div class="col-md-2"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" class="form-control" required maxlength="100" placeholder="visa_type"></div>
    <div class="col-md-2"><label class="form-label">Label <span class="text-danger">*</span></label><input name="label" class="form-control" required maxlength="255" placeholder="Visa Type"></div>
    <div class="col-md-2"><label class="form-label">Type <span class="text-danger">*</span></label><select name="type" class="form-select" required><option value="text">text</option><option value="number">number</option><option value="date">date</option><option value="select">select</option><option value="checkbox">checkbox</option><option value="textarea">textarea</option></select></div>
    <div class="col-md-2"><label class="form-label">Options (JSON or comma list)</label><input name="options" class="form-control" placeholder='["A","B"]'></div>
    <div class="col-md-1"><label class="form-label">Required</label><select name="required" class="form-select"><option value="0">No</option><option value="1">Yes</option></select></div>
    <div class="col-md-1"><label class="form-label">Sort</label><input type="number" name="sort" class="form-control" value="0" min="0" max="9999"></div>
    <div class="col-md-2"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Create</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('workflow.fields'))
<form method="GET" action="{{ route('workflow.fields') }}" class="row g-2">
    <div class="col-md-3"><label class="form-label">Module filter</label><select name="module" class="form-select"><option value="">All modules</option>@foreach(($modules ?? []) as $m)<option value="{{ $m }}" @selected(request('module')===$m)>{{ $m }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Fields</div><div class="card-body">
<x-datatable id="customFieldsTable">
<thead><tr><th>Module</th><th>Name</th><th>Label</th><th>Type</th><th>Required</th><th>Sort</th><th>Active</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($fields ?? []) as $f)
<tr><td>{{ $f->module }}</td><td><code>{{ $f->name }}</code></td><td>{{ $f->label }}</td><td>{{ $f->field_type }}</td><td>{{ $f->is_required ? 'Yes' : 'No' }}</td><td class="tnum">{{ $f->sort_order }}</td><td><x-status-badge :status="$f->active ? 'active' : 'inactive'"/></td>
<td>@if(Route::has('workflow.fields.destroy'))<form method="POST" action="{{ route('workflow.fields.destroy', $f) }}" class="d-inline" onsubmit="return confirm('Delete field?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endif</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($fields ?? null, 'links'))<div class="mt-3">{{ $fields->links() }}</div>@endif
</div></div>
<div class="card shadow-sm"><div class="card-header fw-semibold">Values Viewer @if(request('module'))<span class="text-muted">— module: {{ request('module') }}</span>@endif</div><div class="card-body">
<x-datatable id="customValuesTable">
<thead><tr><th>Field</th><th>Module</th><th>Related</th><th>Value</th><th>Date</th></tr></thead>
<tbody>
@forelse(($values ?? []) as $v)
<tr><td>{{ $v->customField->label ?? '#'.$v->custom_field_id }}</td><td>{{ $v->customField->module ?? '—' }}</td><td>{{ $v->related_type }} #{{ $v->related_id }}</td><td>{{ $v->value }}</td><td>{{ $v->created_at?->format('d M Y') }}</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($values ?? null, 'links'))<div class="mt-3">{{ $values->links() }}</div>@endif
</div></div>
@endsection
