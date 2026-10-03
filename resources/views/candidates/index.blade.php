@extends('layouts.app')
@section('title','Candidates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Candidates</h4>
    <div class="d-flex gap-2">
        <a href="{{ route('candidates.index', ['export' => 'csv'] + request()->query()) }}" class="btn btn-sm btn-outline-success"><i class="fa-solid fa-file-csv me-1"></i>Export CSV</a>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#importModal"><i class="fa-solid fa-upload me-1"></i>Import</button>
        <a href="{{ route('candidates.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Candidate</a>
    </div>
</div>
@include('candidates._filters')
<form method="POST" action="{{ route('candidates.bulk-assign') }}" class="card shadow-sm mb-3" onsubmit="this.querySelector('button[type=submit]').disabled=true">
    @csrf
    <div class="card-body d-flex gap-2 align-items-end flex-wrap">
        <div><label class="form-label small">Bulk assign selected to</label><select name="assigned_to" class="form-select form-select-sm"><option value="">—</option>@foreach(($staff ?? []) as $u)<option value="{{ $u->id }}">{{ $u->name }}</option>@endforeach</select></div>
        <button class="btn btn-sm btn-secondary">Assign</button>
        <div><label class="form-label small">Set status</label><select name="status" class="form-select form-select-sm"><option value="">—</option>@foreach(['NEW','CONTACTED','COUNSELLING','PROFILE_PENDING','PROFILE_COMPLETED','COURSE_SHORTLISTED','APPLICATION_STAGE','VISA_STAGE','ENROLLED','COMPLETED','WITHDRAWN','LOST'] as $s)<option>{{ $s }}</option>@endforeach</select></div>
        <button class="btn btn-sm btn-outline-primary" formaction="{{ route('candidates.bulk-status') }}">Set Status</button>
        <button class="btn btn-sm btn-outline-danger" formaction="{{ route('candidates.archive') }}" onclick="return confirm('Archive selected candidates?')">Archive</button>
        <a href="{{ route('candidates.archived') }}" class="btn btn-sm btn-link">View archived</a>
        <small class="text-muted">Tick rows below first.</small>
    </div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="candidatesTable">
<thead><tr><th><input type="checkbox" id="chkAll"></th><th>UID</th><th>Name</th><th>Email</th><th>Phone</th><th>Destination</th><th>Status</th><th>Assigned</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($candidates ?? []) as $c)
<tr>
    <td><input type="checkbox" name="ids[]" value="{{ $c->id }}" class="rowchk"></td>
    <td>{{ $c->uid ?? $c->id }}</td>
    <td><a href="{{ route('candidates.show', $c) }}">{{ $c->first_name }} {{ $c->last_name }}</a></td>
    <td>{{ $c->email }}</td><td>{{ $c->phone ?? '—' }}</td><td>{{ $c->destination ?? '—' }}</td>
    <td><x-status-badge :status="$c->status ?? 'new'"/></td>
    <td>{{ $c->assignee->name ?? $c->assigned_to ?? '—' }}</td>
    <td class="text-nowrap">
        <a href="{{ route('candidates.show', $c) }}" class="btn btn-sm btn-outline-info">View</a>
        <a href="{{ route('candidates.edit', $c) }}" class="btn btn-sm btn-outline-warning">Edit</a>
        <form method="POST" action="{{ route('candidates.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete candidate?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>
    </td>
</tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(empty($candidates) || (method_exists($candidates,'count') && $candidates->count()==0))<x-empty-state title="No candidates" message="Add your first candidate to get started."/>@endif
@if(isset($candidates) && method_exists($candidates,'links'))<div class="mt-2">{{ $candidates->withQueryString()->links() }}</div>@endif
</div></div>
</form>
<x-modal id="importModal" title="Import Candidates (CSV)">
<form method="POST" action="{{ route('candidates.import') }}" enctype="multipart/form-data">@csrf
<div class="mb-3"><label class="form-label">CSV File <span class="text-danger">*</span></label><input type="file" name="file" accept=".csv" class="form-control" required></div>
<button class="btn btn-primary">Upload</button>
</form>
</x-modal>
@push('scripts')
<script>document.getElementById('chkAll')?.addEventListener('change',function(){document.querySelectorAll('.rowchk').forEach(c=>c.checked=this.checked);});</script>
@endpush
@endsection
