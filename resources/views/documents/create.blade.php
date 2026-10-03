@extends('layouts.app')
@section('title','Upload Document')
@section('content')
<h4 class="mb-3">Upload Document</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">@csrf
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">Candidate <span class="text-danger">*</span></label><select name="candidate_id" class="form-select" required><option value="">Select…</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected(old('candidate_id',request('candidate'))==$c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Application (optional)</label><select name="application_id" class="form-select"><option value="">—</option>@foreach(($applications ?? []) as $a)<option value="{{ $a->id }}" @selected(old('application_id',request('application'))==$a->id)>{{ $a->uid ?? $a->id }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Type <span class="text-danger">*</span></label><select name="type" class="form-select" required>@foreach(['Passport','Academic Transcript','Certificate','IELTS/PTE','SOP','CV','Financial Proof','Offer Letter','CAS','Visa','Other'] as $t)<option @selected(old('type')==$t)>{{ $t }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">File <span class="text-danger">*</span></label><input type="file" name="file" class="form-control @error('file') is-invalid @enderror" required>@error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea></div>
</div>
<div class="d-flex gap-2"><button class="btn btn-primary">Upload</button><a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
@endsection
