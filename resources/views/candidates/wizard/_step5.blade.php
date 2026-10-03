<h5 class="mb-3">Step 5 — Documents ({{ $candidate->documents->count() }} uploaded)</h5>
<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>Document</th><th>Type</th><th>Status</th></tr></thead>
<tbody>@forelse($candidate->documents as $d)<tr><td>{{ $d->original_filename }}</td><td>{{ $d->documentType->name ?? '—' }}</td><td><x-status-badge :status="$d->verification_status ?? ''"/></td></tr>@empty<tr><td colspan="3" class="text-muted">No documents yet.</td></tr>@endforelse</tbody></table></div>
<p class="small text-muted">Upload documents below (PDF/JPG/PNG/DOC, max 10MB each). Verification happens in the Documents module.</p>
<form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="row g-2 align-items-end mb-3">@csrf
<input type="hidden" name="candidate_id" value="{{ $candidate->id }}">
<div class="col-md-5"><label class="form-label small">Type</label><select name="type" class="form-select" required>@foreach(($types ?? []) as $t)<option>{{ $t->name }}</option>@endforeach</select></div>
<div class="col-md-5"><label class="form-label small">File</label><input type="file" name="file" class="form-control" required></div>
<div class="col-md-2"><button class="btn btn-success w-100">Upload</button></div>
</form>
<a href="{{ route('candidates.wizard', [$candidate, 6]) }}" class="btn btn-primary">Review & Submit</a>
