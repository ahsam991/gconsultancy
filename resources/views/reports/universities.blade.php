@extends('layouts.app')
@section('title','Applications by University')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Applications by University</h4>
    <div class="d-flex gap-1">
        @if(Route::has('reports.universities'))
        <a href="{{ route('reports.universities', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-outline-success">CSV</a>
        <a href="{{ route('reports.universities', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success">XLSX</a>
        @endif
        @if(Route::has('reports.index'))
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        @endif
    </div>
</div>

@if(Route::has('reports.universities'))
<x-filter-panel>
<form method="GET" action="{{ route('reports.universities') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary">Run</button></div>
    <div class="col-md-4 small text-muted">Total applications in view: {{ $total ?? 0 }}</div>
</form>
</x-filter-panel>
@endif

<div class="card shadow-sm"><div class="card-body">
<x-datatable id="uniRepTable">
<thead><tr><th>University</th><th>Applications</th><th>Share</th></tr></thead>
<tbody>
@forelse(($rows ?? []) as $r)
<tr><td>{{ $r->university }}</td><td>{{ $r->applications }}</td><td>{{ $r->share }}%</td></tr>
@empty
<tr><td colspan="3" class="text-center text-muted">No applications found.</td></tr>
@endforelse
</tbody>
</x-datatable>
</div></div>
@endsection
