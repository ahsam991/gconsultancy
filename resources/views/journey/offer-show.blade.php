@extends('layouts.app')
@section('title','Offer Details')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Offer — {{ $offer->application->uid ?? ('#'.$offer->application_id) }}</h4>
    @if(Route::has('applications.show') && $offer->application_id)
    <a href="{{ route('applications.show', $offer->application_id) }}" class="btn btn-sm btn-outline-secondary">Back to application</a>
    @endif
</div>

<div class="row g-3 mb-3">
<div class="col-md-6">
<div class="card shadow-sm h-100"><div class="card-body">
    <h6 class="fw-semibold mb-3">Offer Detail</h6>
    <dl class="row mb-0">
        <dt class="col-sm-5">Candidate</dt><dd class="col-sm-7">{{ $offer->application->candidate->first_name ?? '' }} {{ $offer->application->candidate->last_name ?? '' }}</dd>
        <dt class="col-sm-5">University</dt><dd class="col-sm-7">{{ $offer->application->university->name ?? '—' }}</dd>
        <dt class="col-sm-5">Course</dt><dd class="col-sm-7">{{ $offer->application->course->name ?? '—' }}</dd>
        <dt class="col-sm-5">Offer Type</dt><dd class="col-sm-7"><span class="badge bg-primary">{{ $offer->type ?? $offer->offer_type ?? '—' }}</span></dd>
        <dt class="col-sm-5">Offer Date</dt><dd class="col-sm-7">{{ $offer->offer_date ? \Carbon\Carbon::parse($offer->offer_date)->format('d M Y') : '—' }}</dd>
        <dt class="col-sm-5">Deadline</dt><dd class="col-sm-7">{{ $offer->deadline ? \Carbon\Carbon::parse($offer->deadline)->format('d M Y') : '—' }}</dd>
        <dt class="col-sm-5">Deposit</dt><dd class="col-sm-7">{{ $offer->deposit_amount !== null ? number_format($offer->deposit_amount, 2) : '—' }}</dd>
        <dt class="col-sm-5">Scholarship</dt><dd class="col-sm-7">{{ $offer->scholarship ?? $offer->scholarship_amount ?? '—' }}</dd>
        <dt class="col-sm-5">Status</dt><dd class="col-sm-7"><span class="badge bg-secondary">{{ $offer->status ?? 'pending' }}</span></dd>
    </dl>
</div></div>
</div>
<div class="col-md-6">
<div class="card shadow-sm h-100"><div class="card-body">
    <h6 class="fw-semibold mb-3">Required Conditions Progress</h6>
    <div class="d-flex justify-content-between align-items-center mb-2">
        <span class="small text-muted">{{ $requiredDone ?? 0 }} of {{ $requiredTotal ?? 0 }} required conditions completed</span>
        <span class="badge bg-primary">{{ $progress ?? 0 }}%</span>
    </div>
    <div class="progress" role="progressbar" aria-valuenow="{{ $progress ?? 0 }}" aria-valuemin="0" aria-valuemax="100" aria-label="Offer conditions progress">
        <div class="progress-bar" style="width: {{ $progress ?? 0 }}%"></div>
    </div>
    @if(($requiredTotal ?? 0) > 0 && ($requiredDone ?? 0) >= ($requiredTotal ?? 0))
    <div class="alert alert-success mt-3 mb-0">All required conditions are complete. A conditional offer application is promoted to unconditional automatically.</div>
    @endif
</div></div>
</div>
</div>

<div class="card shadow-sm mb-3">
<div class="card-header fw-semibold">Add Condition</div>
<div class="card-body">
<form method="POST" action="{{ Route::has('journey.conditions.store') ? route('journey.conditions.store', $offer->id) : url('/journey/offers/'.$offer->id.'/conditions') }}">
    @csrf
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label" for="condition_text">Condition <span class="text-danger">*</span></label>
            <input name="condition_text" id="condition_text" value="{{ old('condition_text') }}" class="form-control @error('condition_text') is-invalid @enderror" placeholder="e.g. Provide final degree transcript" required>
            @error('condition_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" class="form-control @error('deadline') is-invalid @enderror">
            @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="notes">Notes</label>
            <input name="notes" id="notes" value="{{ old('notes') }}" class="form-control @error('notes') is-invalid @enderror" placeholder="Condition notes">
            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 mb-3">
            <div class="form-check">
                <input type="checkbox" name="is_required" id="is_required" value="1" class="form-check-input" @checked(old('is_required', true))>
                <label class="form-check-label" for="is_required">Required condition</label>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Add Condition</button>
</form>
</div>
</div>

<div class="card shadow-sm"><div class="card-body">
<h6 class="fw-semibold mb-3">Conditions Checklist</h6>
<div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Condition</th><th>Required</th><th>Submitted</th><th>Verified</th><th>Completed</th><th>Deadline</th><th class="text-end">Actions</th></tr></thead>
<tbody>
@forelse(($conditions ?? []) as $cond)
<tr class="{{ $cond->is_completed ? 'table-success' : '' }}">
    <td>{{ $cond->condition_text }}@if($cond->notes)<br><span class="small text-muted">{{ $cond->notes }}</span>@endif</td>
    <td>@if($cond->is_required)<span class="badge bg-danger">Required</span>@else<span class="badge bg-secondary">Optional</span>@endif</td>
    <td>@if($cond->is_submitted)<span class="badge bg-info text-dark">Submitted</span>@else<span class="text-muted">—</span>@endif</td>
    <td>@if($cond->is_verified)<span class="badge bg-primary">Verified</span>@else<span class="text-muted">—</span>@endif</td>
    <td>@if($cond->is_completed)<span class="badge bg-success">Completed</span>@else<span class="text-muted">—</span>@endif</td>
    <td class="small">{{ $cond->deadline ? \Carbon\Carbon::parse($cond->deadline)->format('d M Y') : '—' }}</td>
    <td class="text-end text-nowrap">
        @if(Route::has('journey.conditions.toggle'))
        <form method="POST" action="{{ route('journey.conditions.toggle', $cond->id) }}" class="d-inline">@csrf @method('PATCH')<button type="submit" class="btn btn-sm {{ $cond->is_completed ? 'btn-outline-secondary' : 'btn-outline-success' }}">{{ $cond->is_completed ? 'Reopen' : 'Complete' }}</button></form>
        @endif
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#condEdit{{ $cond->id }}">Edit</button>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">No conditions recorded for this offer.</td></tr>
@endforelse
</tbody>
</table></div>
</div></div>

@foreach(($offer->conditions ?? []) as $cond)
<div class="modal fade" id="condEdit{{ $cond->id }}" tabindex="-1" aria-labelledby="condEditLabel{{ $cond->id }}" aria-hidden="true">
<div class="modal-dialog"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title" id="condEditLabel{{ $cond->id }}">Edit Condition</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<form method="POST" action="{{ Route::has('journey.conditions.update') ? route('journey.conditions.update', $cond->id) : url('/journey/conditions/'.$cond->id) }}">
@csrf @method('PUT')
<div class="modal-body">
    <div class="mb-3"><label class="form-label">Condition</label><input name="condition_text" value="{{ old('condition_text', $cond->condition_text) }}" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Deadline</label><input type="date" name="deadline" value="{{ old('deadline', $cond->deadline ? \Carbon\Carbon::parse($cond->deadline)->format('Y-m-d') : '') }}" class="form-control"></div>
    <div class="mb-3"><label class="form-label">Notes</label><input name="notes" value="{{ old('notes', $cond->notes) }}" class="form-control"></div>
    <div class="form-check mb-2"><input type="checkbox" name="is_required" value="1" class="form-check-input" id="condReq{{ $cond->id }}" @checked(old('is_required', (bool) $cond->is_required))><label class="form-check-label" for="condReq{{ $cond->id }}">Required</label></div>
    <div class="form-check mb-2"><input type="checkbox" name="is_submitted" value="1" class="form-check-input" id="condSub{{ $cond->id }}" @checked(old('is_submitted', (bool) $cond->is_submitted))><label class="form-check-label" for="condSub{{ $cond->id }}">Submitted</label></div>
    <div class="form-check mb-2"><input type="checkbox" name="is_verified" value="1" class="form-check-input" id="condVer{{ $cond->id }}" @checked(old('is_verified', (bool) $cond->is_verified))><label class="form-check-label" for="condVer{{ $cond->id }}">Verified</label></div>
    <div class="form-check mb-2"><input type="checkbox" name="is_completed" value="1" class="form-check-input" id="condCom{{ $cond->id }}" @checked(old('is_completed', (bool) $cond->is_completed))><label class="form-check-label" for="condCom{{ $cond->id }}">Completed</label></div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save changes</button></div>
</form>
</div></div>
</div>
@endforeach
@endsection
