<h5 class="mb-3">Step 6 — Review & Submit</h5>
<dl class="row">
<dt class="col-4">Name</dt><dd class="col-8">{{ $candidate->first_name }} {{ $candidate->last_name }}</dd>
<dt class="col-4">Contact</dt><dd class="col-8">{{ $candidate->email }} · {{ $candidate->phone }}</dd>
<dt class="col-4">Passport</dt><dd class="col-8">{{ $candidate->passport_no ?? '—' }}</dd>
<dt class="col-4">Qualifications</dt><dd class="col-8 tnum">{{ $candidate->qualifications->count() }}</dd>
<dt class="col-4">English Tests</dt><dd class="col-8 tnum">{{ $candidate->englishTests->count() }}</dd>
<dt class="col-4">Emergency Contacts</dt><dd class="col-8 tnum">{{ $candidate->emergencyContacts->count() }}</dd>
<dt class="col-4">Documents</dt><dd class="col-8 tnum">{{ $candidate->documents->count() }}</dd>
</dl>
<form method="POST" action="{{ route('candidates.wizard.store', [$candidate, 6]) }}">@csrf
<button class="btn btn-success" name="save_draft" value="0">Submit Profile</button>
<button class="btn btn-outline-secondary" name="save_draft" value="1">Save Draft</button>
<a href="{{ route('candidates.wizard', [$candidate, 1]) }}" class="btn btn-link">Back to start</a>
</form>
