@extends('layouts.app')
@section('title','Data Access Logs')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Data Access Logs</h4>
    @if(Route::has('gdpr.requests'))<a href="{{ route('gdpr.requests') }}" class="btn btn-sm btn-outline-secondary">GDPR Requests</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('gdpr.access-logs'))
<form method="GET" action="{{ route('gdpr.access-logs') }}" class="row g-2">
    <div class="col-md-4"><label class="form-label">Candidate</label><select name="candidate_id" class="form-select"><option value="">All</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected((string) request('candidate_id') === (string) $c->id)>#{{ $c->id }} — {{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Action contains</label><input name="action" value="{{ request('action') }}" class="form-control" placeholder="e.g. gdpr_export"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="accessLogsTable">
<thead><tr><th>When</th><th>Who</th><th>Candidate</th><th>Action</th><th>Purpose</th><th>IP</th></tr></thead>
<tbody>
@forelse(($logs ?? []) as $l)
<tr><td class="text-nowrap">{{ $l->created_at?->format('d M Y H:i') }}</td><td>{{ $l->user->name ?? 'User #'.$l->user_id }}</td><td>{{ $l->candidate->first_name ?? '' }} {{ $l->candidate->last_name ?? '' }} <span class="text-muted">#{{ $l->candidate_id }}</span></td><td><code>{{ $l->action }}</code></td><td>{{ $l->purpose ?? '—' }}</td><td>{{ $l->ip ?? '—' }}</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($logs ?? null, 'links'))<div class="mt-3">{{ $logs->links() }}</div>@endif
</div></div>
@endsection
