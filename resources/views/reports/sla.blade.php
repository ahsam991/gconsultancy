@extends('layouts.app')
@section('title','SLA Breaches')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">SLA Breaches</h4>
    <div class="d-flex gap-1">
        @if(Route::has('reports.sla'))
        <a href="{{ route('reports.sla', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-outline-success">CSV</a>
        <a href="{{ route('reports.sla', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success">XLSX</a>
        @endif
        @if(Route::has('reports.index'))
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        @endif
    </div>
</div>

@if(!empty($notice))
<div class="alert alert-info">{{ $notice }}</div>
@endif

<div class="card shadow-sm"><div class="card-body">
<x-datatable id="slaTable">
<thead><tr><th>ID</th><th>Policy</th><th>Related</th><th>Detected</th><th>Resolved</th><th>Notified</th></tr></thead>
<tbody>
@forelse(($breaches ?? []) as $b)
<tr>
    <td>{{ $b->id ?? '—' }}</td>
    <td>{{ $b->policy->name ?? $b->sla_policy_id ?? '—' }}</td>
    <td>{{ $b->related_type ?? '—' }} #{{ $b->related_id ?? '—' }}</td>
    <td>{{ $b->detected_at ?? '—' }}</td>
    <td>{{ $b->resolved_at ?? '—' }}</td>
    <td>{{ !empty($b->notified) ? 'Yes' : 'No' }}</td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No SLA breaches recorded.</td></tr>
@endforelse
</tbody>
</x-datatable>
@if(($breaches ?? null) instanceof \Illuminate\Pagination\AbstractPaginator)
<div class="mt-2">{{ $breaches->links() }}</div>
@endif
</div></div>
@endsection
