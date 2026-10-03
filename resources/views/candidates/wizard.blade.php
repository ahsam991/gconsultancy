@extends('layouts.app')
@section('title','Profile Wizard — ' . ($candidate->uid ?? ''))
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<h4 class="mb-0">Profile Wizard <small class="text-muted">{{ $candidate->first_name }} {{ $candidate->last_name }} ({{ $candidate->uid }})</small></h4>
<span class="badge bg-info tnum">{{ $completion ?? 0 }}% complete</span>
</div>
<div class="progress mb-3" style="height:8px"><div class="progress-bar" style="width:{{ $completion ?? 0 }}%"></div></div>
<ul class="nav nav-pills mb-3 flex-wrap gap-1">
@foreach(($steps ?? []) as $n => $label)
<li class="nav-item"><a class="nav-link py-1 px-2 small {{ ($step ?? 1)==$n ? 'active' : '' }} {{ ($step ?? 1)>$n ? 'bg-success text-white' : '' }}" href="{{ route('candidates.wizard', [$candidate, $n]) }}">{{ $n }}. {{ $label }}</a></li>
@endforeach
</ul>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body">
@include('candidates.wizard._step' . ($step ?? 1))
</div></div>
@endsection
