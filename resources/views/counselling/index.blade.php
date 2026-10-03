@extends('layouts.app')
@section('title','Counselling Sessions')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Counselling Sessions</h4>
    @if(Route::has('counselling.create'))
    <a href="{{ route('counselling.create', ['candidate_id' => request('candidate_id')]) }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Session</a>
    @endif
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<form method="GET" action="{{ Route::has('counselling.index') ? route('counselling.index') : url('/counselling') }}" class="row g-2 align-items-end">
    <div class="col-md-5">
        <label class="form-label small" for="candidate_id">Candidate</label>
        <select name="candidate_id" id="candidate_id" class="form-select">
            <option value="">All candidates</option>
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(request('candidate_id') == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label small" for="status">Status</label>
        <select name="status" id="status" class="form-select">
            <option value="">All statuses</option>
            @foreach(['scheduled','completed','follow_up','cancelled'] as $s)
            <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 d-flex gap-1">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ Route::has('counselling.index') ? route('counselling.index') : url('/counselling') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
</div></div>
<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Date</th><th>Candidate</th><th>Purpose</th><th>Staff</th><th>Follow-up</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
<tbody>
@forelse(($sessions ?? []) as $s)
<tr>
    <td>{{ $s->session_date ? \Carbon\Carbon::parse($s->session_date)->format('d M Y') : '—' }}</td>
    <td>{{ $s->candidate->first_name ?? '' }} {{ $s->candidate->last_name ?? '' }}</td>
    <td>{{ \Illuminate\Support\Str::limit($s->purpose, 60) }}</td>
    <td>{{ $s->staff->name ?? '—' }}</td>
    <td>{{ $s->followup_date ? \Carbon\Carbon::parse($s->followup_date)->format('d M Y') : '—' }}</td>
    <td><span class="badge bg-secondary">{{ $s->status ?? 'scheduled' }}</span></td>
    <td class="text-end text-nowrap">
        @if(Route::has('counselling.edit'))
        <a href="{{ route('counselling.edit', $s) }}" class="btn btn-sm btn-outline-warning">View / Edit</a>
        @endif
        @if(Route::has('counselling.destroy'))
        <form method="POST" action="{{ route('counselling.destroy', $s) }}" class="d-inline" onsubmit="return confirm('Delete this session?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">No counselling sessions found.</td></tr>
@endforelse
</tbody>
</table></div>
{{ ($sessions ?? null)?->links() }}
</div></div>
@endsection
