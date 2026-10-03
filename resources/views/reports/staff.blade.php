@extends('layouts.app')
@section('title','Staff Performance Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Staff Performance</h4><a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
<x-filter-panel><form method="GET" action="{{ route('reports.staff') }}" class="row g-2 align-items-end w-100">
<div class="col-md-4"><label class="form-label small">Search</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or email"></div>
<div class="col-md-2"><button class="btn btn-primary">Run</button></div>
</form></x-filter-panel>
<div class="card shadow-sm"><div class="card-body"><x-datatable id="repStaff"><thead><tr><th>Staff</th><th>Role</th><th>Candidates</th><th>Applications</th></tr></thead><tbody>
@forelse(($rows ?? []) as $u)<tr><td>{{ $u->name }}<br><small class="text-muted">{{ $u->email }}</small></td><td>{{ ucfirst($u->role->name ?? '—') }}</td><td class="tnum">{{ $u->candidates_count ?? 0 }}</td><td class="tnum">{{ $u->applications_count ?? 0 }}</td></tr>@empty @endforelse
</tbody></x-datatable></div></div>
@endsection
