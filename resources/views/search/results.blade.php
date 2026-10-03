@extends('layouts.app')
@section('title','Search')
@section('content')
<h4 class="mb-3">Search @if(!empty($q))<small class="text-muted">“{{ $q }}”</small>@endif</h4>
<form method="GET" action="{{ route('search') }}" class="card shadow-sm mb-3"><div class="card-body d-flex gap-2">
<input name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="Name, email, phone, UID, university, course…" minlength="2" required>
<button class="btn btn-primary">Search</button>
</div></form>
@if(!empty($q))
<div class="row">
<div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Candidates ({{ ($candidates ?? collect())->count() }})</div><div class="list-group list-group-flush">@forelse(($candidates ?? []) as $c)<a class="list-group-item" href="{{ route('candidates.show', $c) }}">{{ $c->first_name }} {{ $c->last_name }} <small class="text-muted">{{ $c->uid }} · {{ $c->email }}</small></a>@empty<span class="list-group-item text-muted">No matches.</span>@endforelse</div></div></div>
<div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Applications ({{ ($applications ?? collect())->count() }})</div><div class="list-group list-group-flush">@forelse(($applications ?? []) as $a)<a class="list-group-item" href="{{ route('applications.show', $a) }}">{{ $a->uid }} <small class="text-muted">{{ $a->status }}</small></a>@empty<span class="list-group-item text-muted">No matches.</span>@endforelse</div></div></div>
<div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Universities ({{ ($universities ?? collect())->count() }})</div><div class="list-group list-group-flush">@forelse(($universities ?? []) as $u)<a class="list-group-item" href="{{ route('universities.show', $u) }}">{{ $u->name }}</a>@empty<span class="list-group-item text-muted">No matches.</span>@endforelse</div></div></div>
<div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Courses ({{ ($courses ?? collect())->count() }})</div><div class="list-group list-group-flush">@forelse(($courses ?? []) as $c)<a class="list-group-item" href="{{ route('courses.show', $c) }}">{{ $c->name }}</a>@empty<span class="list-group-item text-muted">No matches.</span>@endforelse</div></div></div>
@if(($leads ?? collect())->isNotEmpty())
<div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Leads ({{ $leads->count() }})</div><div class="list-group list-group-flush">@foreach($leads as $l)<a class="list-group-item" href="{{ route('leads.show', $l) }}">{{ $l->first_name }} {{ $l->last_name }} <small class="text-muted">{{ $l->email }}</small></a>@endforeach</div></div></div>
@endif
</div>
@endif
@endsection
