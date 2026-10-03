@extends('layouts.app')
@section('title','Sponsorships')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Sponsorships</h4>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<form method="GET" action="{{ Route::has('support.sponsorships') ? route('support.sponsorships') : url('/support/sponsorships') }}" class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label small" for="filter_candidate">Candidate</label>
        <select name="candidate_id" id="filter_candidate" class="form-select" onchange="this.form.submit()">
            <option value="">All candidates</option>
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(request('candidate_id') == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex gap-1">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ Route::has('support.sponsorships') ? route('support.sponsorships') : url('/support/sponsorships') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
</div></div>

<div class="card shadow-sm mb-3">
<div class="card-header fw-semibold">Add Sponsorship</div>
<div class="card-body">
<form method="POST" action="{{ Route::has('support.sponsorships.store') ? route('support.sponsorships.store') : url('/support/sponsorships') }}">
    @csrf
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label" for="candidate_id">Candidate <span class="text-danger">*</span></label>
            <select name="candidate_id" id="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror" required>
                <option value="">Select candidate</option>
                @foreach(($candidates ?? []) as $c)
                <option value="{{ $c->id }}" @selected(old('candidate_id', request('candidate_id')) == $c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>
                @endforeach
            </select>
            @error('candidate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="sponsor_name">Sponsor Name <span class="text-danger">*</span></label>
            <input name="sponsor_name" id="sponsor_name" value="{{ old('sponsor_name') }}" class="form-control @error('sponsor_name') is-invalid @enderror" placeholder="e.g. Ahmed Rahman" required maxlength="255">
            @error('sponsor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="relationship">Relationship</label>
            <input name="relationship" id="relationship" value="{{ old('relationship') }}" class="form-control @error('relationship') is-invalid @enderror" placeholder="e.g. Father" maxlength="100">
            @error('relationship')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="occupation">Occupation</label>
            <input name="occupation" id="occupation" value="{{ old('occupation') }}" class="form-control @error('occupation') is-invalid @enderror" placeholder="e.g. Business owner" maxlength="255">
            @error('occupation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="country">Country</label>
            <input name="country" id="country" value="{{ old('country') }}" class="form-control @error('country') is-invalid @enderror" placeholder="e.g. Bangladesh" maxlength="100">
            @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="contact">Contact</label>
            <input name="contact" id="contact" value="{{ old('contact') }}" class="form-control @error('contact') is-invalid @enderror" placeholder="Phone or email" maxlength="255">
            @error('contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="income">Annual Income</label>
            <input type="number" step="0.01" min="0" name="income" id="income" value="{{ old('income') }}" class="form-control @error('income') is-invalid @enderror">
            @error('income')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="funding_amount">Funding Amount</label>
            <input type="number" step="0.01" min="0" name="funding_amount" id="funding_amount" value="{{ old('funding_amount') }}" class="form-control @error('funding_amount') is-invalid @enderror">
            @error('funding_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="evidence_path">Evidence Path</label>
            <input name="evidence_path" id="evidence_path" value="{{ old('evidence_path') }}" class="form-control @error('evidence_path') is-invalid @enderror" placeholder="e.g. documents/bank-statement.pdf" maxlength="255">
            @error('evidence_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 mb-3">
            <div class="form-check">
                <input type="checkbox" name="verified" id="verified" value="1" class="form-check-input" @checked(old('verified'))>
                <label class="form-check-label" for="verified">Sponsor verified</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Add Sponsorship</button>
</form>
</div>
</div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Candidate</th><th>Sponsor</th><th>Relationship</th><th>Funding</th><th>Verified</th><th class="text-end">Actions</th></tr></thead>
<tbody>
@forelse(($sponsorships ?? []) as $sp)
<tr>
    <td>{{ $sp->candidate->first_name ?? '' }} {{ $sp->candidate->last_name ?? '' }}</td>
    <td>{{ $sp->sponsor_name }}<br><span class="small text-muted">{{ $sp->occupation ?? '' }}{{ $sp->country ? ' · '.$sp->country : '' }}</span></td>
    <td>{{ $sp->relationship ?? '—' }}</td>
    <td>{{ $sp->funding_amount !== null ? number_format($sp->funding_amount, 2) : '—' }}</td>
    <td>@if($sp->verified)<span class="badge bg-success">Verified</span>@else<span class="badge bg-warning text-dark">Unverified</span>@endif</td>
    <td class="text-end text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#spEdit{{ $sp->id }}">Edit</button>
        @if(Route::has('support.sponsorships.destroy'))
        <form method="POST" action="{{ route('support.sponsorships.destroy', $sp->id) }}" class="d-inline" onsubmit="return confirm('Delete this sponsorship?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted py-4">No sponsorship records found.</td></tr>
@endforelse
</tbody>
</table></div>
{{ ($sponsorships ?? null)?->links() }}
</div></div>

@foreach(($sponsorships ?? []) as $sp)
<div class="modal fade" id="spEdit{{ $sp->id }}" tabindex="-1" aria-labelledby="spEditLabel{{ $sp->id }}" aria-hidden="true">
<div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title" id="spEditLabel{{ $sp->id }}">Edit Sponsorship</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<form method="POST" action="{{ Route::has('support.sponsorships.update') ? route('support.sponsorships.update', $sp->id) : url('/support/sponsorships/'.$sp->id) }}">
@csrf @method('PUT')
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Sponsor Name</label><input name="sponsor_name" value="{{ old('sponsor_name', $sp->sponsor_name) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Relationship</label><input name="relationship" value="{{ old('relationship', $sp->relationship) }}" class="form-control" maxlength="100"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Occupation</label><input name="occupation" value="{{ old('occupation', $sp->occupation) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Country</label><input name="country" value="{{ old('country', $sp->country) }}" class="form-control" maxlength="100"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Contact</label><input name="contact" value="{{ old('contact', $sp->contact) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Evidence Path</label><input name="evidence_path" value="{{ old('evidence_path', $sp->evidence_path) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Annual Income</label><input type="number" step="0.01" min="0" name="income" value="{{ old('income', $sp->income) }}" class="form-control"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Funding Amount</label><input type="number" step="0.01" min="0" name="funding_amount" value="{{ old('funding_amount', $sp->funding_amount) }}" class="form-control"></div>
        <div class="col-12 mb-3"><div class="form-check"><input type="checkbox" name="verified" value="1" class="form-check-input" id="spVerified{{ $sp->id }}" @checked(old('verified', (bool) $sp->verified))><label class="form-check-label" for="spVerified{{ $sp->id }}">Sponsor verified</label></div></div>
    </div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save changes</button></div>
</form>
</div></div>
</div>
@endforeach
@endsection
