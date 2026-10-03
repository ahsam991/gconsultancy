@extends('layouts.app')
@section('title','Marketing Report')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Marketing by Source</h4>
    <div class="d-flex gap-1">
        @if(Route::has('reports.marketing'))
        <a href="{{ route('reports.marketing', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-outline-success">CSV</a>
        <a href="{{ route('reports.marketing', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success">XLSX</a>
        @endif
        @if(Route::has('reports.index'))
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        @endif
    </div>
</div>

@if(Route::has('reports.marketing'))
<x-filter-panel>
<form method="GET" action="{{ route('reports.marketing') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-3"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-2"><button class="btn btn-primary">Run</button></div>
</form>
</x-filter-panel>
@endif

<div class="card shadow-sm"><div class="card-body">
<x-datatable id="mktTable">
<thead><tr><th>Source</th><th>Leads</th><th>Converted</th><th>Conversion</th></tr></thead>
<tbody>
@forelse(($rows ?? []) as $r)
<tr><td>{{ $r->source ?? 'Unknown' }}</td><td>{{ $r->leads }}</td><td>{{ $r->converted }}</td><td>{{ $r->conversion }}%</td></tr>
@empty
<tr><td colspan="4" class="text-center text-muted">No leads grouped by source.</td></tr>
@endforelse
</tbody>
</x-datatable>
</div></div>
@endsection
