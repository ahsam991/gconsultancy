@extends('layouts.app')
@section('title','Deadlines & Expiries')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Deadlines &amp; Expiries</h4>
    <div class="d-flex gap-1">
        @if(Route::has('reports.deadlines'))
        <a href="{{ route('reports.deadlines', array_merge(request()->query(), ['format' => 'csv', 'table' => 'intakes'])) }}" class="btn btn-sm btn-outline-success">Intakes CSV</a>
        <a href="{{ route('reports.deadlines', array_merge(request()->query(), ['format' => 'xlsx', 'table' => 'intakes'])) }}" class="btn btn-sm btn-outline-success">Intakes XLSX</a>
        @endif
        @if(Route::has('reports.index'))
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        @endif
    </div>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
<h6>Intake deadlines within 14 days (until {{ $intakeLimit ?? '' }})</h6>
<x-datatable id="deadIntakes">
<thead><tr><th>Application</th><th>Candidate</th><th>University</th><th>Intake</th><th>Deadline</th></tr></thead>
<tbody>
@forelse(($intakes ?? []) as $a)
<tr>
    <td>@if(Route::has('applications.show'))<a href="{{ route('applications.show', $a) }}">{{ $a->uid ?? $a->id }}</a>@else{{ $a->uid ?? $a->id }}@endif</td>
    <td>{{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</td>
    <td>{{ $a->university->name ?? '—' }}</td>
    <td>{{ $a->intake->name ?? '—' }}</td>
    <td><x-status-badge :status="$a->intake->deadline ? $a->intake->deadline->toDateString() : ''"/></td>
</tr>
@empty
<tr><td colspan="5" class="text-center text-muted">No intake deadlines in the next 14 days.</td></tr>
@endforelse
</tbody>
</x-datatable>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-2">
<h6 class="mb-0">Documents expiring within 30 days (until {{ $docLimit ?? '' }})</h6>
@if(Route::has('reports.deadlines'))
<a href="{{ route('reports.deadlines', array_merge(request()->query(), ['format' => 'csv', 'table' => 'docs'])) }}" class="btn btn-sm btn-outline-success">Docs CSV</a>
@endif
</div>
<x-datatable id="deadDocs">
<thead><tr><th>Candidate</th><th>Document</th><th>Expiry date</th></tr></thead>
<tbody>
@forelse(($docs ?? []) as $d)
<tr>
    <td>{{ $d->candidate->first_name ?? '' }} {{ $d->candidate->last_name ?? '' }}</td>
    <td>{{ $d->documentType->name ?? $d->original_filename }}</td>
    <td>{{ $d->expiry_date?->toDateString() ?? '—' }}</td>
</tr>
@empty
<tr><td colspan="3" class="text-center text-muted">No documents expiring in the next 30 days.</td></tr>
@endforelse
</tbody>
</x-datatable>
</div></div>
@endsection
