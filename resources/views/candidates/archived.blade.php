@extends('layouts.app')
@section('title','Archived Candidates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Archived Candidates</h4><a href="{{ route('candidates.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="archivedTable">
<thead><tr><th>UID</th><th>Name</th><th>Email</th><th>Archived At</th><th>Actions</th></tr></thead>
<tbody>@forelse(($candidates ?? []) as $c)<tr><td>{{ $c->uid }}</td><td>{{ $c->first_name }} {{ $c->last_name }}</td><td>{{ $c->email }}</td><td>{{ $c->deleted_at ?? '' }}</td>
<td><form method="POST" action="{{ route('candidates.restore', $c->id) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Restore</button></form></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
