@extends('layouts.app')
@section('title','Mail Logs')
@section('content')
<h4 class="mb-3">Mail Logs</h4>
<div class="card shadow-sm mb-3"><div class="card-body">
@if(Route::has('system.mail-logs'))
<form method="GET" action="{{ route('system.mail-logs') }}" class="row g-2">
    <div class="col-md-3"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['queued','sent','failed'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-outline-primary">Filter</button></div>
</form>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="mailLogsTable">
<thead><tr><th>To</th><th>Subject</th><th>Template</th><th>Status</th><th>Error</th><th>Sent</th></tr></thead>
<tbody>
@forelse(($logs ?? []) as $l)
<tr><td>{{ $l->to_email }}</td><td>{{ $l->subject }}</td><td>{{ $l->template_slug ?? '—' }}</td><td><x-status-badge :status="$l->status"/></td><td class="small text-muted">{{ $l->error ?? '—' }}</td><td>{{ $l->sent_at?->format('d M Y H:i') ?? '—' }}</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($logs ?? null, 'links'))<div class="mt-3">{{ $logs->links() }}</div>@endif
</div></div>
@endsection
