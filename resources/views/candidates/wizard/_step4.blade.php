<h5 class="mb-3">Step 4 — Emergency Contact</h5>
<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>Name</th><th>Relationship</th><th>Phone</th></tr></thead>
<tbody>@forelse($candidate->emergencyContacts as $e)<tr><td>{{ $e->name }}</td><td>{{ $e->relationship }}</td><td>{{ $e->phone }}</td></tr>@empty<tr><td colspan="3" class="text-muted">None added yet.</td></tr>@endforelse</tbody></table></div>
<form method="POST" action="{{ route('candidates.wizard.store', [$candidate, 4]) }}">@csrf
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">Name <span class="text-danger">*</span></label><input name="name" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Relationship <span class="text-danger">*</span></label><input name="relationship" class="form-control" required placeholder="e.g. Father"></div>
<div class="col-md-6 mb-3"><label class="form-label">Phone <span class="text-danger">*</span></label><input name="phone" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
<div class="col-12 mb-3"><label class="form-label">Address</label><textarea name="address" rows="2" class="form-control"></textarea></div>
</div>
<button class="btn btn-success">Save Contact</button>
<a href="{{ route('candidates.wizard', [$candidate, 5]) }}" class="btn btn-primary">Continue</a>
</form>
