@extends('layouts.app')
@section('title','GDPR Requests')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">GDPR Requests (SAR)</h4>
    @if(Route::has('gdpr.access-logs'))<a href="{{ route('gdpr.access-logs') }}" class="btn btn-sm btn-outline-secondary">Data Access Logs</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">New Request</div><div class="card-body">
@if(Route::has('gdpr.requests.store'))
<form method="POST" action="{{ route('gdpr.requests.store') }}" class="row g-2">@csrf
    <div class="col-md-4"><label class="form-label">Candidate <span class="text-danger">*</span></label><select name="candidate_id" class="form-select" required><option value="">Select…</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}">#{{ $c->id }} — {{ $c->first_name }} {{ $c->last_name }} ({{ $c->email }})</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Type <span class="text-danger">*</span></label><select name="type" class="form-select" required><option value="export">Export (SAR download)</option><option value="anonymize">Anonymize (keep financial records)</option><option value="delete">Delete (soft-delete)</option></select></div>
    <div class="col-md-3"><label class="form-label">Notes</label><input name="notes" class="form-control" maxlength="2000"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Create</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('gdpr.requests'))
<form method="GET" action="{{ route('gdpr.requests') }}" class="row g-2">
    <div class="col-md-3"><label class="form-label">Candidate</label><select name="candidate_id" class="form-select"><option value="">All</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected((string) request('candidate_id') === (string) $c->id)>#{{ $c->id }} — {{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label">Type</label><select name="type" class="form-select"><option value="">All</option>@foreach(['export','anonymize','delete'] as $t)<option @selected(request('type')===$t) value="{{ $t }}">{{ $t }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['pending','in_progress','completed','rejected'] as $s)<option @selected(request('status')===$s) value="{{ $s }}">{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="gdprRequestsTable">
<thead><tr><th>ID</th><th>Candidate</th><th>Type</th><th>Status</th><th>Requested By</th><th>Completed</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($gdprRequests ?? []) as $r)
<tr>
    <td class="tnum">{{ $r->id }}</td>
    <td>{{ $r->candidate->first_name ?? '' }} {{ $r->candidate->last_name ?? '' }} <span class="text-muted">#{{ $r->candidate_id }}</span></td>
    <td><span class="badge bg-info">{{ $r->type }}</span></td>
    <td><x-status-badge :status="$r->status"/></td>
    <td>{{ $r->requester->name ?? '—' }}</td>
    <td>{{ $r->completed_at?->format('d M Y H:i') ?? '—' }}</td>
    <td class="text-nowrap">
        @if(Route::has('gdpr.requests.download') && $r->type === 'export')<a href="{{ route('gdpr.requests.download', $r) }}" class="btn btn-sm btn-outline-primary">JSON Dump</a>@endif
        @if(Route::has('gdpr.requests.update'))
        <form method="POST" action="{{ route('gdpr.requests.update', $r) }}" class="d-inline">@csrf @method('PATCH')
            <input type="hidden" name="notes" value="{{ $r->notes }}">
            <select name="status" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                @foreach(['pending','in_progress','completed','rejected'] as $s)<option value="{{ $s }}" @selected($r->status===$s)>{{ $s }}</option>@endforeach
            </select>
        </form>
        @endif
    </td>
</tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($gdprRequests ?? null, 'links'))<div class="mt-3">{{ $gdprRequests->links() }}</div>@endif
<p class="small text-muted mt-2 mb-0">Completing an export request streams a JSON download (candidate + applications + documents list). Completing anonymize replaces PII with ANONYMIZED_&lt;id&gt; and keeps financial records. Completing delete soft-deletes the candidate. ZIP archive is optional and not required.</p>
</div></div>
@endsection
