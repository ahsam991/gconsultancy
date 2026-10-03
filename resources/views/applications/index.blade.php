@extends('layouts.app')
@section('title','Applications')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Applications</h4><a href="{{ route('applications.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Application</a></div>
<x-filter-panel>
<form method="GET" action="{{ route('applications.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['draft','submitted','offer_received','deposit_paid','cas_issued','visa_applied','visa_granted','visa_refused','enrolled','deferred','cancelled'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">University</label><select name="university_id" class="form-select"><option value="">All</option>@foreach(($universities ?? []) as $u)<option value="{{ $u->id }}" @selected(request('university_id')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Course</label><select name="course_id" class="form-select"><option value="">All</option>@foreach(($courses ?? []) as $c)<option value="{{ $c->id }}" @selected(request('course_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="appsTable">
<thead><tr><th>UID</th><th>Candidate</th><th>University</th><th>Course</th><th>Status</th><th>Priority</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($applications ?? []) as $a)
<tr><td>{{ $a->uid ?? $a->id }}</td><td><a href="{{ route('candidates.show', $a->candidate_id) }}">{{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</a></td><td>{{ $a->university->name ?? '—' }}</td><td>{{ $a->course->name ?? '—' }}</td><td><x-status-badge :status="$a->status ?? ''"/></td><td><x-status-badge :status="$a->priority ?? 'normal'"/></td>
<td class="text-nowrap"><a href="{{ route('applications.show', $a) }}" class="btn btn-sm btn-outline-info">View</a><a href="{{ route('applications.edit', $a) }}" class="btn btn-sm btn-outline-warning">Edit</a><form method="POST" action="{{ route('applications.destroy', $a) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(isset($applications) && method_exists($applications,'links'))<div class="mt-2">{{ $applications->withQueryString()->links() }}</div>@endif
</div></div>
@endsection
