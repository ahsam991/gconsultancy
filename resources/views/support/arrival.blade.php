@extends('layouts.app')
@section('title','Arrival Record')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Arrival — {{ ($candidate->first_name ?? '') }} {{ ($candidate->last_name ?? '') }}</h4>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<div class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label small" for="candidate_switch">Candidate</label>
        <select id="candidate_switch" class="form-select" onchange="if(this.value){ window.location.href = '{{ Route::has('support.arrival') ? route('support.arrival', '__CID__') : url('/support/arrival/__CID__') }}'.replace('__CID__', this.value); }">
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(($candidate->id ?? null) == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
    </div>
</div>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('support.arrival.update') ? route('support.arrival.update', $candidate->id ?? 0) : url('/support/arrival/'.($candidate->id ?? 0)) }}">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="form-check border rounded p-3">
                <input type="checkbox" name="arrived" id="arrived" value="1" class="form-check-input ms-0 me-2 float-none" @checked(old('arrived', (bool) ($arrival->arrived ?? false)))>
                <label class="form-check-label fw-medium" for="arrived">Arrived</label>
                @error('arrived')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="form-check border rounded p-3">
                <input type="checkbox" name="university_registered" id="university_registered" value="1" class="form-check-input ms-0 me-2 float-none" @checked(old('university_registered', (bool) ($arrival->university_registered ?? false)))>
                <label class="form-check-label fw-medium" for="university_registered">University Registered</label>
                @error('university_registered')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="form-check border rounded p-3">
                <input type="checkbox" name="accommodation_confirmed" id="accommodation_confirmed" value="1" class="form-check-input ms-0 me-2 float-none" @checked(old('accommodation_confirmed', (bool) ($arrival->accommodation_confirmed ?? false)))>
                <label class="form-check-label fw-medium" for="accommodation_confirmed">Accommodation Confirmed</label>
                @error('accommodation_confirmed')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="arrival_date">Arrival Date</label>
            <input type="date" name="arrival_date" id="arrival_date" value="{{ old('arrival_date', isset($arrival->arrival_date) ? \Carbon\Carbon::parse($arrival->arrival_date)->format('Y-m-d') : '') }}" class="form-control @error('arrival_date') is-invalid @enderror">
            @error('arrival_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="brp_number">BRP Number</label>
            <input name="brp_number" id="brp_number" value="{{ old('brp_number', $arrival->brp_number ?? '') }}" class="form-control @error('brp_number') is-invalid @enderror" placeholder="e.g. ZX1234567" maxlength="255">
            @error('brp_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 mb-3">
            <label class="form-label" for="notes">Notes</label>
            <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror" placeholder="Arrival notes, pickup details, registration remarks">{{ old('notes', $arrival->notes ?? '') }}</textarea>
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Save Arrival Record</button>
</form>
</div></div>
@endsection
