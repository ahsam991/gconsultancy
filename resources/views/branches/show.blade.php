@extends('layouts.app')
@section('title','Branch')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">{{ $branch->name }} <small class="text-muted">({{ $branch->code }})</small> <x-status-badge :status="$branch->active ? 'active' : 'inactive'"/></h4>
    <div class="d-flex gap-2">
        @if(Route::has('branches.edit'))<a href="{{ route('branches.edit', $branch) }}" class="btn btn-sm btn-warning">Edit</a>@endif
        @if(Route::has('branches.index'))<a href="{{ route('branches.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>@endif
    </div>
</div>
<div class="card shadow-sm mb-3"><div class="card-body row">
    <div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Code</dt><dd class="col-8">{{ $branch->code }}</dd><dt class="col-4">Address</dt><dd class="col-8">{{ $branch->address ?? '—' }}</dd><dt class="col-4">City</dt><dd class="col-8">{{ $branch->city ?? '—' }}</dd><dt class="col-4">Country</dt><dd class="col-8">{{ $branch->country ?? '—' }}</dd></dl></div>
    <div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Phone</dt><dd class="col-8">{{ $branch->phone ?? '—' }}</dd><dt class="col-4">Email</dt><dd class="col-8">{{ $branch->email ?? '—' }}</dd><dt class="col-4">Manager</dt><dd class="col-8">{{ $branch->manager->name ?? '—' }}</dd><dt class="col-4">Active</dt><dd class="col-8">{{ $branch->active ? 'Yes' : 'No' }}</dd></dl></div>
</div></div>
<div class="row">
    <div class="col-md-6"><div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Staff ({{ $branch->users->count() }})</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th></tr></thead><tbody>@forelse($branch->users as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td></tr>@empty<tr><td colspan="2" class="text-muted p-3">No staff linked.</td></tr>@endforelse</tbody></table></div></div></div></div>
    <div class="col-md-6"><div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Candidates ({{ $branch->candidates->count() }})</div><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th></tr></thead><tbody>@forelse($branch->candidates->take(20) as $c)<tr><td>{{ $c->first_name }} {{ $c->last_name }}</td><td>{{ $c->email }}</td></tr>@empty<tr><td colspan="2" class="text-muted p-3">No candidates linked.</td></tr>@endforelse</tbody></table></div></div></div></div>
</div>
@if(Route::has('branches.destroy'))
<form method="POST" action="{{ route('branches.destroy', $branch) }}" onsubmit="return confirm('Delete this branch?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete Branch</button></form>
@endif
@endsection
