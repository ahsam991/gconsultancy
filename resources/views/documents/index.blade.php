@extends('layouts.app')
@section('title','Documents')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Documents</h4><a href="{{ route('documents.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Upload</a></div>
<x-filter-panel>
<form method="GET" action="{{ route('documents.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Candidate</label><select name="candidate_id" class="form-select"><option value="">All</option>@foreach(($candidates ?? []) as $c)<option value="{{ $c->id }}" @selected(request('candidate_id')==$c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Type</label><select name="type" class="form-select"><option value="">All</option>@foreach(['Passport','Academic Transcript','Certificate','IELTS/PTE','SOP','CV','Financial Proof','Offer Letter','CAS','Visa','Other'] as $t)<option @selected(request('type')==$t)>{{ $t }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['pending','verified','rejected'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="docsTable">
<thead><tr><th>File</th><th>Candidate</th><th>Type</th><th>Version</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($documents ?? []) as $d)<tr>
<td>{{ $d->original_name ?? $d->name ?? '' }}</td><td><a href="{{ route('candidates.show', $d->candidate_id) }}">{{ $d->candidate->first_name ?? '' }} {{ $d->candidate->last_name ?? '' }}</a></td>
<td>{{ $d->type ?? '—' }}</td><td>v{{ $d->version ?? 1 }}</td><td><x-status-badge :status="$d->status ?? 'pending'"/></td>
<td class="text-nowrap"><a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary">Download</a><form method="POST" action="{{ route('documents.verify', $d) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Verify</button></form><form method="POST" action="{{ route('documents.reject', $d) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-danger">Reject</button></form><form method="POST" action="{{ route('documents.destroy', $d) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
