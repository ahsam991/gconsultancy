@extends('layouts.app')
@section('title','GDPR Consents')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">GDPR Consents</h4>
    @if(Route::has('gdpr.access-logs'))<a href="{{ route('gdpr.access-logs') }}" class="btn btn-sm btn-outline-secondary">Data Access Logs</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('gdpr.consents'))
<form method="GET" action="{{ route('gdpr.consents') }}" class="row g-2">
    <div class="col-md-4"><label class="form-label">Candidate</label><select name="candidate_id" class="form-select"><option value="">All candidates</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected((string) request('candidate_id') === (string) $c->id)>#{{ $c->id }} — {{ $c->first_name }} {{ $c->last_name }} ({{ $c->email }})</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Consent type</label><input name="consent_type" value="{{ request('consent_type') }}" class="form-control" placeholder="e.g. marketing"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="consentsTable">
<thead><tr><th>ID</th><th>Candidate</th><th>Type</th><th>Given</th><th>IP</th><th>Policy</th><th>Date</th></tr></thead>
<tbody>
@forelse(($consents ?? []) as $c)
<tr><td class="tnum">{{ $c->id }}</td><td>{{ $c->candidate->first_name ?? '' }} {{ $c->candidate->last_name ?? '' }} <span class="text-muted">#{{ $c->candidate_id }}</span></td><td>{{ $c->consent_type }}</td><td><x-status-badge :status="$c->given ? 'active' : 'inactive'"/></td><td>{{ $c->ip ?? '—' }}</td><td>{{ $c->policy_version ?? '—' }}</td><td>{{ $c->created_at?->format('d M Y H:i') }}</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($consents ?? null, 'links'))<div class="mt-3">{{ $consents->links() }}</div>@endif
</div></div>
@endsection
