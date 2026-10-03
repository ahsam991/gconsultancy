@extends('layouts.app')
@section('title', 'Eligibility Check')
@section('content')
<h4 class="mb-3">Eligibility Check</h4>
<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="GET" action="{{ Route::has('engagement.eligibility') ? route('engagement.eligibility') : url('/engagement/eligibility') }}" class="row g-2 align-items-end">
            <div class="col-md-2">
                <label class="form-label small" for="gpa">GPA (0–4)</label>
                <input type="number" step="0.01" min="0" max="4" name="gpa" id="gpa" value="{{ old('gpa', $filters['gpa'] ?? request('gpa')) }}" class="form-control @error('gpa') is-invalid @enderror">
                @error('gpa')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label small" for="ielts">IELTS Score</label>
                <input type="number" step="0.5" min="0" max="9" name="ielts" id="ielts" value="{{ old('ielts', $filters['ielts'] ?? request('ielts')) }}" class="form-control @error('ielts') is-invalid @enderror">
                @error('ielts')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small" for="budget">Max Budget (£)</label>
                <input type="number" min="0" name="budget" id="budget" value="{{ old('budget', $filters['budget'] ?? request('budget')) }}" class="form-control @error('budget') is-invalid @enderror">
                @error('budget')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label small" for="destination">Destination</label>
                <select name="destination" id="destination" class="form-select @error('destination') is-invalid @enderror">
                    <option value="">Any destination</option>
                    @foreach(($countries ?? []) as $ct)
                        <option value="{{ $ct->id }}" @selected(old('destination', $filters['destination'] ?? request('destination')) == $ct->id)>{{ $ct->name }}</option>
                    @endforeach
                </select>
                @error('destination')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button class="btn btn-primary">Check</button>
                <a href="{{ Route::has('engagement.eligibility') ? route('engagement.eligibility') : url('/engagement/eligibility') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
        <p class="small text-muted mt-2 mb-0">Filters: tuition fee within budget and IELTS requirement within your score. GPA is recorded for counselling reference.</p>
    </div>
</div>

@if(isset($courses) && count($courses))
<div class="row">
    @foreach($courses as $c)
    <div class="col-md-4 mb-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6>{{ $c->name }}</h6>
                <p class="small text-muted mb-1">{{ $c->university->name ?? '' }} · {{ $c->university->country->name ?? '' }}</p>
                <p class="mb-1 small">Fee: <strong>£{{ number_format($c->tuition_fee ?? 0, 2) }}</strong> · IELTS: <strong>{{ $c->ielts_required ?? '—' }}</strong></p>
                <div class="d-flex gap-2">
                    @if(Route::has('courses.show'))
                        <a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-info">Details</a>
                    @else
                        <a href="{{ url('/crm/courses/' . $c->id) }}" class="btn btn-sm btn-outline-info">Details</a>
                    @endif
                    @if(Route::has('engagement.compare'))
                        <a href="{{ route('engagement.compare', ['ids' => [$c->id]]) }}" class="btn btn-sm btn-outline-secondary">Compare</a>
                    @else
                        <a href="{{ url('/engagement/compare?ids[]=' . $c->id) }}" class="btn btn-sm btn-outline-secondary">Compare</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@if(method_exists($courses, 'links')){{ $courses->withQueryString()->links() }}@endif
@elseif(request()->filled(['budget', 'ielts', 'ielts_score', 'destination', 'gpa']))
<div class="alert alert-info">No eligible courses found for the given budget and IELTS score.</div>
@endif
@endsection
