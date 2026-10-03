@extends('layouts.app')
@section('title','Status Transitions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Status Transitions</h4>
    @if(Route::has('workflow.templates'))<a href="{{ route('workflow.templates') }}" class="btn btn-sm btn-outline-secondary">Templates</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">New Transition</div><div class="card-body">
@if(Route::has('workflow.transitions.store'))
<form method="POST" action="{{ route('workflow.transitions.store') }}" class="row g-2">@csrf
    <div class="col-md-3"><label class="form-label">Template <span class="text-danger">*</span></label><select name="workflow_template_id" class="form-select" required><option value="">Select…</option>@foreach(($templates ?? []) as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">From <span class="text-danger">*</span></label><input name="from_status" class="form-control" required maxlength="255" placeholder="e.g. NEW"></div>
    <div class="col-md-2"><label class="form-label">To <span class="text-danger">*</span></label><input name="to_status" class="form-control" required maxlength="255" placeholder="e.g. CONTACTED"></div>
    <div class="col-md-2"><label class="form-label">Required permission</label><input name="required_permission" class="form-control" maxlength="255"></div>
    <div class="col-md-3"><label class="form-label">Automation (JSON)</label><input name="automation" class="form-control" placeholder='{"notify":"staff"}'></div>
    <div class="col-md-2"><label class="form-label">Active</label><select name="active" class="form-select"><option value="1">Yes</option><option value="0">No</option></select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Create</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('workflow.transitions'))
<form method="GET" action="{{ route('workflow.transitions') }}" class="row g-2">
    <div class="col-md-4"><label class="form-label">Template</label><select name="workflow_template_id" class="form-select"><option value="">All</option>@foreach(($templates ?? []) as $t)<option value="{{ $t->id }}" @selected((string) request('workflow_template_id') === (string) $t->id)>{{ $t->name }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="transitionsTable">
<thead><tr><th>Template</th><th>From</th><th>To</th><th>Permission</th><th>Automation</th><th>Active</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($transitions ?? []) as $tr)
<tr>
    <td>{{ $tr->template->name ?? '#'.$tr->workflow_template_id }}</td>
    <td><code>{{ $tr->from_status }}</code></td><td><code>{{ $tr->to_status }}</code></td>
    <td>{{ $tr->required_permission ?? '—' }}</td>
    <td><code class="small">{{ $tr->automation ? json_encode($tr->automation) : '—' }}</code></td>
    <td><x-status-badge :status="$tr->active ? 'active' : 'inactive'"/></td>
    <td class="text-nowrap">
        @if(Route::has('workflow.transitions.destroy'))
        <form method="POST" action="{{ route('workflow.transitions.destroy', $tr) }}" class="d-inline" onsubmit="return confirm('Delete transition?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
        @endif
    </td>
</tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($transitions ?? null, 'links'))<div class="mt-3">{{ $transitions->links() }}</div>@endif
</div></div>
@endsection
