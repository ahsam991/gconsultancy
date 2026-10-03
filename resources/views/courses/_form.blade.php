@php $co = $course ?? null; @endphp
<div class="row">
    <div class="col-md-6 mb-3"><label class="form-label">University <span class="text-danger">*</span></label><select name="university_id" class="form-select" required><option value="">Select…</option>@foreach(($universities ?? []) as $u)<option value="{{ $u->id }}" @selected(old('university_id',$co->university_id ?? request('university'))==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" value="{{ old('name', $co->name ?? '') }}" class="form-control @error('name') is-invalid @enderror" required>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4 mb-3"><label class="form-label">Level</label><select name="level" class="form-select">@foreach(['Foundation','Undergraduate','Postgraduate','Diploma','PhD','English'] as $l)<option @selected(old('level',$co->level ?? '')==$l)>{{ $l }}</option>@endforeach</select></div>
    <div class="col-md-4 mb-3"><label class="form-label">Subject</label><input name="subject" value="{{ old('subject', $co->subject ?? '') }}" class="form-control"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Duration</label><input name="duration" value="{{ old('duration', $co->duration ?? '') }}" class="form-control" placeholder="e.g. 3 years"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Tuition Fee (£)</label><input type="number" step="0.01" name="tuition_fee" value="{{ old('tuition_fee', $co->tuition_fee ?? '') }}" class="form-control"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Deposit (£)</label><input type="number" step="0.01" name="deposit" value="{{ old('deposit', $co->deposit ?? '') }}" class="form-control"></div>
    <div class="col-md-4 mb-3"><label class="form-label">Intakes (comma separated)</label><input name="intakes" value="{{ old('intakes', $co->intakes ?? 'Jan, May, Sep') }}" class="form-control"></div>
    <div class="col-12 mb-3"><label class="form-label">Entry Requirements</label><textarea name="requirements" rows="3" class="form-control">{{ old('requirements', $co->requirements ?? '') }}</textarea></div>
</div>
