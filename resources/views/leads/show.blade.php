@extends('layouts.app')
@section('title','Lead Detail')
@section('content')
<h4 class="mb-3">{{ $lead->first_name ?? '' }} {{ $lead->last_name ?? $lead->name ?? '' }} <x-status-badge :status="$lead->status ?? 'new'"/></h4>
<div class="row">
<div class="col-md-6"><div class="card shadow-sm mb-3"><div class="card-body"><dl class="row mb-0">
<dt class="col-4">Email</dt><dd class="col-8">{{ $lead->email ?? '—' }}</dd>
<dt class="col-4">Phone</dt><dd class="col-8">{{ $lead->phone ?? '—' }}</dd>
<dt class="col-4">Source</dt><dd class="col-8">{{ $lead->source->name ?? '—' }}</dd>
<dt class="col-4">Destination</dt><dd class="col-8">{{ $lead->destination ?? '—' }}</dd>
<dt class="col-4">Notes</dt><dd class="col-8">{{ $lead->notes ?? '—' }}</dd>
</dl></div></div></div>
<div class="col-md-6"><div class="card shadow-sm"><div class="card-header fw-semibold">Convert to Candidate</div><div class="card-body">
<form method="POST" action="{{ route('leads.convert', $lead) }}">@csrf
<div class="mb-2"><label class="form-label small">Assign Staff</label><select name="assigned_to" class="form-select"><option value="">Unassigned</option>@foreach(($staff ?? $users ?? []) as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
<button class="btn btn-success">Convert Lead</button>
<a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Back</a>
</form>
</div></div></div>
</div>
@endsection
