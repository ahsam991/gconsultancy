@extends('layouts.app')
@section('title','Teams')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Teams</h4><a href="{{ route('teams.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Team</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="teamsTable">
<thead><tr><th>Name</th><th>Manager</th><th>Members</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($teams ?? []) as $t)<tr><td><a href="{{ route('teams.show', $t) }}">{{ $t->name }}</a></td><td>{{ $t->manager->name ?? '—' }}</td><td class="tnum">{{ $t->members_count ?? 0 }}</td><td><x-status-badge :status="$t->active ? 'active' : 'inactive'"/></td>
<td class="text-nowrap"><a href="{{ route('teams.edit', $t) }}" class="btn btn-sm btn-outline-warning">Edit</a></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
