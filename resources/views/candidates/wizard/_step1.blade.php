<h5 class="mb-3">Step 1 — Personal Info</h5>
<form method="POST" action="{{ route('candidates.wizard.store', [$candidate, 1]) }}">@csrf
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">First Name <span class="text-danger">*</span></label><input name="first_name" value="{{ old('first_name', $candidate->first_name) }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Last Name <span class="text-danger">*</span></label><input name="last_name" value="{{ old('last_name', $candidate->last_name) }}" class="form-control" required></div>
<div class="col-md-4 mb-3"><label class="form-label">DOB</label><input type="date" name="dob" value="{{ old('dob', $candidate->dob?->format('Y-m-d')) }}" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Gender</label><select name="gender" class="form-select"><option value="">—</option>@foreach(['Male','Female','Other'] as $g)<option @selected(old('gender', $candidate->gender)==$g)>{{ $g }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label class="form-label">Nationality</label><input name="nationality" value="{{ old('nationality', $candidate->nationality) }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Passport No</label><input name="passport_no" value="{{ old('passport_no', $candidate->passport_no) }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Passport Expiry</label><input type="date" name="passport_expiry" value="{{ old('passport_expiry', $candidate->passport_expiry?->format('Y-m-d')) }}" class="form-control"></div>
<div class="col-12 mb-3"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-control">{{ old('address', $candidate->address) }}</textarea></div>
<div class="col-md-4 mb-3"><label class="form-label">City</label><input name="city" value="{{ old('city', $candidate->city) }}" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Preferred Destination</label><select name="preferred_destination" class="form-select"><option value="">—</option>@foreach(['UK','USA','Canada','Australia','Europe'] as $d)<option @selected(old('preferred_destination', $candidate->preferred_destination)==$d)>{{ $d }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label class="form-label">Referral Source</label><input name="referral_source" value="{{ old('referral_source', $candidate->referral_source) }}" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Preferred Level</label><input name="preferred_level" value="{{ old('preferred_level', $candidate->preferred_level) }}" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Preferred Subject</label><input name="preferred_subject" value="{{ old('preferred_subject', $candidate->preferred_subject) }}" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Assigned Staff</label><select name="assigned_staff_id" class="form-select"><option value="">—</option>@foreach(($staff ?? []) as $u)<option value="{{ $u->id }}" @selected(old('assigned_staff_id', $candidate->assigned_staff_id)==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
</div>
<button class="btn btn-primary" name="save_draft" value="0">Save & Continue</button>
<button class="btn btn-outline-secondary" name="save_draft" value="1">Save Draft</button>
</form>
