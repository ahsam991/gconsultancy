@php $list = $notes ?? []; @endphp
<ul class="list-group mb-3">
@forelse($list as $n)
<li class="list-group-item"><div class="d-flex justify-content-between"><strong>{{ $n->user->name ?? 'Staff' }}</strong><small class="text-muted">{{ $n->created_at ?? '' }}</small></div><p class="mb-1">{{ $n->body ?? $n->content ?? '' }}</p>
<form method="POST" action="{{ route('notes.destroy', $n) }}" class="d-inline" onsubmit="return confirm('Delete note?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0">Delete</button></form></li>
@empty
<li class="list-group-item text-muted">No notes yet.</li>
@endforelse
</ul>
<form method="POST" action="{{ route('notes.store') }}">@csrf
<input type="hidden" name="notable_type" value="{{ $notableType ?? 'candidate' }}">
<input type="hidden" name="notable_id" value="{{ $notableId ?? ($candidate->id ?? ($application->id ?? '')) }}">
<div class="mb-2"><textarea name="body" rows="2" class="form-control" placeholder="Add a note…" required>{{ old('body') }}</textarea></div>
<button class="btn btn-sm btn-primary">Add Note</button>
</form>
<hr>
<form method="POST" action="{{ route('communications.store') }}">@csrf
<input type="hidden" name="notable_type" value="{{ $notableType ?? 'candidate' }}">
<input type="hidden" name="notable_id" value="{{ $notableId ?? '' }}">
<div class="row g-2 align-items-end"><div class="col-md-4"><label class="form-label small">Log Communication</label><select name="channel" class="form-select form-select-sm"><option value="call">Call</option><option value="email">Email</option><option value="sms">SMS</option><option value="meeting">Meeting</option></select></div><div class="col-md-6"><input name="summary" class="form-control form-control-sm" placeholder="Summary" required></div><div class="col-md-2"><button class="btn btn-sm btn-outline-primary">Log</button></div></div>
</form>
