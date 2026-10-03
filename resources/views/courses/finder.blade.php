@extends('layouts.app')
@section('title','Course Finder')
@section('content')
<h4 class="mb-3">Course Finder</h4>
<x-filter-panel>
<form method="GET" action="{{ route('courses.finder') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-2"><label class="form-label small">Destination</label><select name="country" class="form-select"><option value="">Any</option>@foreach(['UK','Canada','Australia','USA','New Zealand','Ireland'] as $ct)<option @selected(request('country')==$ct)>{{ $ct }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Level</label><select name="level" class="form-select"><option value="">Any</option>@foreach(['Foundation','Undergraduate','Postgraduate','Diploma','PhD'] as $l)<option @selected(request('level')==$l)>{{ $l }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Subject Keyword</label><input name="subject" value="{{ request('subject') }}" class="form-control" placeholder="e.g. Computer Science"></div>
    <div class="col-md-2"><label class="form-label small">Max Fee (£)</label><input type="number" name="max_fee" value="{{ request('max_fee') }}" class="form-control"></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Search</button><a href="{{ route('courses.finder') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="row">
@forelse(($results ?? $courses ?? []) as $c)
<div class="col-md-4 mb-3"><div class="card shadow-sm h-100"><div class="card-body"><h6>{{ $c->name }}</h6><p class="small text-muted mb-1">{{ $c->university->name ?? '' }} · {{ $c->level ?? '' }}</p><p class="fw-bold mb-2">£{{ number_format($c->tuition_fee ?? 0,2) }}</p><div class="d-flex gap-2"><a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-info">Details</a><a href="{{ route('applications.create', ['course' => $c->id]) }}" class="btn btn-sm btn-success">Apply</a></div></div></div></div>
@empty
<div class="col-12"><x-empty-state title="No courses found" message="Try widening your filters."/></div>
@endforelse
</div>
@if(isset($results) && method_exists($results,'links')){{ $results->withQueryString()->links() }}@endif
@endsection
