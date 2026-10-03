@extends('layouts.app')
@section('title','Admissions Funnel')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Admissions Funnel</h4>
    <div class="d-flex gap-1">
        @if(Route::has('reports.funnel'))
        <a href="{{ route('reports.funnel', array_merge(request()->query(), ['format' => 'csv'])) }}" class="btn btn-sm btn-outline-success">CSV</a>
        <a href="{{ route('reports.funnel', array_merge(request()->query(), ['format' => 'xlsx'])) }}" class="btn btn-sm btn-outline-success">XLSX</a>
        @endif
        @if(Route::has('reports.index'))
        <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
        @endif
    </div>
</div>

@if(Route::has('reports.funnel'))
<x-filter-panel>
<form method="GET" action="{{ route('reports.funnel') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Staff</label><select name="staff_id" class="form-select"><option value="">All staff</option>@foreach(($staff ?? []) as $u)<option value="{{ $u->id }}" @selected(request('staff_id')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Country</label><select name="country" class="form-select"><option value="">All</option>@foreach(($countries ?? []) as $c)<option value="{{ $c->name }}" @selected(request('country')==$c->name)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">From</label><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
    <div class="col-md-2"><label class="form-label small">To</label><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary">Run</button><a href="{{ route('reports.funnel') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
@endif

@php $max = max(1, collect($stages ?? [])->max('count') ?? 1); @endphp
<div class="card shadow-sm"><div class="card-body">
@forelse(($stages ?? []) as $s)
@php $pct = $max > 0 ? round($s['count'] / $max * 100) : 0; @endphp
<div class="mb-3">
    <div class="d-flex justify-content-between small mb-1"><span class="fw-medium">{{ $s['label'] }}</span><span>{{ $s['count'] }} @if(!is_null($s['of_prev'])) · {{ $s['of_prev'] }}% of previous @endif @if(!is_null($s['of_first'])) · {{ $s['of_first'] }}% of leads @endif</span></div>
    <div class="progress" style="height:22px"><div class="progress-bar" role="progressbar" style="width:{{ $pct }}%" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">{{ $pct }}%</div></div>
</div>
@empty
<p class="text-muted mb-0">No funnel data for the selected filters.</p>
@endforelse
</div></div>
@endsection
