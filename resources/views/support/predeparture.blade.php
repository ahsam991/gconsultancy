@extends('layouts.app')
@section('title','Pre-departure Checklist')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Pre-departure Checklist — {{ ($candidate->first_name ?? '') }} {{ ($candidate->last_name ?? '') }}</h4>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<form method="GET" action="{{ Route::has('support.predeparture') ? route('support.predeparture', ($candidate->id ?? request('candidate_id'))) : url('/support/predeparture') }}" class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label small" for="candidate_switch">Candidate</label>
        <select id="candidate_switch" class="form-select" onchange="if(this.value){ window.location.href = '{{ Route::has('support.predeparture') ? route('support.predeparture', '__CID__') : url('/support/predeparture/__CID__') }}'.replace('__CID__', this.value); }">
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(($candidate->id ?? null) == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
    </div>
</form>
</div></div>

<div class="card shadow-sm mb-3"><div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-2">
    <span class="fw-semibold">Overall progress</span>
    <span class="badge bg-primary">{{ $progress ?? 0 }}%</span>
</div>
<div class="progress" role="progressbar" aria-valuenow="{{ $progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100" aria-label="Pre-departure progress">
    <div class="progress-bar" style="width: {{ $progress ?? 0 }}%"></div>
</div>
<p class="small text-muted mt-2 mb-0">{{ ($checklist ? collect($steps ?? [])->filter(fn($s) => (bool) $checklist->{$s})->count() : 0) }} of {{ count($steps ?? []) }} steps completed.</p>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('support.predeparture.update') ? route('support.predeparture.update', $candidate->id ?? 0) : url('/support/predeparture/'.($candidate->id ?? 0)) }}">
    @csrf
    @method('PUT')
    <div class="row">
        @foreach(($steps ?? []) as $step)
        <div class="col-md-6 mb-3">
            <div class="form-check border rounded p-3">
                <input type="checkbox" name="{{ $step }}" id="step_{{ $step }}" value="1" class="form-check-input ms-0 me-2 float-none" @checked(old($step, (bool) ($checklist->{$step} ?? false)))>
                <label class="form-check-label fw-medium" for="step_{{ $step }}">{{ ucwords(str_replace('_',' ',$step)) }}</label>
                @error($step)<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
        @endforeach
    </div>
    <button type="submit" class="btn btn-primary">Save Checklist</button>
</form>
</div></div>
@endsection
