<h5 class="mb-3">Step 2 — Academic Qualifications</h5>
<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>Level</th><th>Institution</th><th>Year</th><th>Result</th></tr></thead>
<tbody>@forelse($candidate->qualifications as $q)<tr><td>{{ $q->level }}</td><td>{{ $q->institution }}</td><td>{{ $q->passing_year }}</td><td>{{ $q->result }}</td></tr>@empty<tr><td colspan="4" class="text-muted">None added yet.</td></tr>@endforelse</tbody></table></div>
<form method="POST" action="{{ route('candidates.wizard.store', [$candidate, 2]) }}">@csrf
<h6>Add qualification</h6>
<div class="row">
<div class="col-md-4 mb-3"><label class="form-label">Level <span class="text-danger">*</span></label><select name="level" class="form-select" required>@foreach(['SSC','HSC','Diploma',"Bachelor's","Master's",'Other'] as $l)<option>{{ $l }}</option>@endforeach</select></div>
<div class="col-md-8 mb-3"><label class="form-label">Institution <span class="text-danger">*</span></label><input name="institution" class="form-control" required></div>
<div class="col-md-4 mb-3"><label class="form-label">Country</label><input name="country" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Passing Year</label><input type="number" name="passing_year" min="1980" max="2100" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Result</label><input name="result" class="form-control" placeholder="e.g. GPA 3.8"></div>
<div class="col-md-6 mb-3"><label class="form-label">Grading Scale</label><input name="grading_scale" class="form-control" placeholder="e.g. out of 4.0"></div>
</div>
<button class="btn btn-success">Add</button>
<a href="{{ route('candidates.wizard', [$candidate, 3]) }}" class="btn btn-primary">Continue</a>
<button class="btn btn-outline-secondary" name="save_draft" value="1">Save Draft</button>
</form>
