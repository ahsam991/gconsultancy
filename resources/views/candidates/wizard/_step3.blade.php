<h5 class="mb-3">Step 3 — English Test</h5>
<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>Test</th><th>Overall</th><th>L/R/W/S</th><th>Date</th></tr></thead>
<tbody>@forelse($candidate->englishTests as $t)<tr><td>{{ $t->test_type }}</td><td>{{ $t->overall }}</td><td>{{ $t->listening }}/{{ $t->reading }}/{{ $t->writing }}/{{ $t->speaking }}</td><td>{{ $t->test_date?->format('d M Y') }}</td></tr>@empty<tr><td colspan="4" class="text-muted">None added yet.</td></tr>@endforelse</tbody></table></div>
<form method="POST" action="{{ route('candidates.wizard.store', [$candidate, 3]) }}">@csrf
<h6>Add test</h6>
<div class="row">
<div class="col-md-4 mb-3"><label class="form-label">Test <span class="text-danger">*</span></label><select name="test_type" class="form-select" required>@foreach(['IELTS','TOEFL','PTE','Duolingo','MOI'] as $t)<option>{{ $t }}</option>@endforeach</select></div>
<div class="col-md-4 mb-3"><label class="form-label">Overall</label><input type="number" step="0.5" min="0" max="9" name="overall" class="form-control"></div>
<div class="col-md-4 mb-3"><label class="form-label">Test Date</label><input type="date" name="test_date" class="form-control"></div>
@foreach(['listening'=>'Listening','reading'=>'Reading','writing'=>'Writing','speaking'=>'Speaking'] as $f=>$l)<div class="col-md-3 mb-3"><label class="form-label">{{ $l }}</label><input type="number" step="0.5" min="0" max="9" name="{{ $f }}" class="form-control"></div>@endforeach
<div class="col-md-6 mb-3"><label class="form-label">Expiry</label><input type="date" name="expiry_date" class="form-control"></div>
</div>
<button class="btn btn-success">Add</button>
<a href="{{ route('candidates.wizard', [$candidate, 4]) }}" class="btn btn-primary">Continue</a>
<button class="btn btn-outline-secondary" name="save_draft" value="1">Save Draft</button>
</form>
