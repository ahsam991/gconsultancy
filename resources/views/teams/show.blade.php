@extends('layouts.app')
@section('title','Team')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<h4 class="mb-0">{{ $team->name }} <x-status-badge :status="$team->active ? 'active' : 'inactive'"/></h4>
<div class="d-flex gap-2"><a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('teams.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
</div>
<div class="card shadow-sm mb-3"><div class="card-body row">
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Manager</dt><dd class="col-8">{{ $team->manager->name ?? '—' }}</dd></dl></div>
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Description</dt><dd class="col-8">{{ $team->description ?? '—' }}</dd></dl></div>
</div></div>
<div class="card shadow-sm"><div class="card-header fw-semibold">Members ({{ $team->members->count() }})</div><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
<tbody>@forelse($team->members as $m)<tr><td>{{ $m->name }}</td><td>{{ $m->email }}</td><td>{{ $m->role->name ?? '—' }}</td></tr>@empty<tr><td colspan="3" class="text-muted p-3">No members assigned.</td></tr>@endforelse</tbody></table></div>
</div></div>
@endsection
