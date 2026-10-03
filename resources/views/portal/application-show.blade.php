@extends('layouts.app')
@section('title','Application ' . ($application->uid ?? ''))
@section('content')
<div class="container-fluid">
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">{{ $application->uid ?? 'Application' }}</h1><a href="{{ route('portal.applications') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
<div class="card shadow-sm mb-3"><div class="card-body">
<div class="row"><div class="col-6"><small class="text-muted">University</small><div class="fw-semibold">{{ $application->university->name ?? '—' }}</div></div>
<div class="col-6"><small class="text-muted">Course</small><div class="fw-semibold">{{ $application->course->name ?? '—' }}</div></div></div>
<div class="mt-2"><x-status-badge :status="$application->status ?? ''"/></div>
</div></div>
<div class="card shadow-sm"><div class="card-header fw-semibold">Status Timeline</div><div class="card-body p-0">
<div class="table-responsive"><table class="table mb-0"><thead><tr><th>From</th><th>To</th><th>When</th><th>Note</th></tr></thead>
<tbody>@forelse(($application->statusHistory ?? []) as $h)<tr><td>{{ $h->previous_status ?? '—' }}</td><td><x-status-badge :status="$h->new_status ?? ''"/></td><td>{{ $h->created_at ?? '' }}</td><td>{{ $h->note ?? $h->reason ?? '' }}</td></tr>@empty<tr><td colspan="4" class="text-muted p-3">No updates yet.</td></tr>@endforelse</tbody></table></div>
</div></div>
</div>
@endsection
