@extends('layouts.app')
@section('title','Application Detail')
@section('content')
@php
$a = $application;
$steps = ['DRAFT','PROFILE_CHECK','DOCUMENT_PENDING','READY_TO_APPLY','SUBMITTED','ACKNOWLEDGED','UNDER_REVIEW','CONDITIONAL_OFFER','UNCONDITIONAL_OFFER','DEPOSIT_REQUIRED','DEPOSIT_PAID','CAS_REQUESTED','CAS_ISSUED','VISA_PREPARATION','VISA_APPLIED','VISA_APPROVED','ENROLLED'];
$cur = array_search($a->status ?? 'DRAFT', $steps);
if($cur === false) $cur = 0;
@endphp
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Application {{ $a->uid ?? $a->id }} <x-status-badge :status="$a->status ?? ''"/></h4>
    <div class="d-flex gap-2"><a href="{{ route('applications.edit', $a) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
    <div class="d-flex flex-wrap gap-1 mb-3">
        @foreach($steps as $i => $s)
        <div class="flex-fill text-center small px-1 py-2 rounded {{ $i < $cur ? 'bg-success text-white' : ($i == $cur ? 'bg-primary text-white' : 'bg-light text-muted') }}">{{ ucwords(str_replace('_',' ',$s)) }}</div>
        @endforeach
    </div>
    <form method="POST" action="{{ route('applications.status', $a) }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4"><label class="form-label small">Change Status <span class="text-danger">*</span></label><select name="status" class="form-select" required>@foreach(($allowed ?? ['WITHDRAWN','REJECTED','CLOSED']) as $s)<option @selected(($a->status ?? '')==$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label small">Reason / Note</label><input name="reason" class="form-control" placeholder="Reason for change" value="{{ old('reason') }}"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Update</button></div>
    </form>
</div></div>
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#aOver">Overview</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aDocs">Documents</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aTime">Timeline</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aNotes">Notes</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aTasks">Tasks</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aJourney">Journey</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#aFin">Finance</button></li>
</ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="aOver"><div class="card shadow-sm"><div class="card-body row">
    <div class="col-md-6"><dl class="row mb-0">
    <dt class="col-4">Candidate</dt><dd class="col-8"><a href="{{ route('candidates.show', $a->candidate_id) }}">{{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</a></dd>
    <dt class="col-4">University</dt><dd class="col-8">{{ $a->university->name ?? '—' }}</dd>
    <dt class="col-4">Course</dt><dd class="col-8">{{ $a->course->name ?? '—' }}</dd>
    <dt class="col-4">Intake</dt><dd class="col-8">{{ $a->intake->name ?? '—' }}</dd>
    </dl></div>
    <div class="col-md-6"><dl class="row mb-0">
    <dt class="col-4">Priority</dt><dd class="col-8"><x-status-badge :status="$a->priority ?? 'normal'"/></dd>
    <dt class="col-4">Counsellor</dt><dd class="col-8">{{ $a->assignedStaff->name ?? '—' }}</dd>
    <dt class="col-4">Notes</dt><dd class="col-8">{{ $a->notes ?? '—' }}</dd>
    </dl></div>
    @if(!empty($statusHistory) || !empty($a->statusHistory))<div class="col-12 mt-3"><h6>Status History</h6><div class="table-responsive"><table class="table table-striped"><thead><tr><th>From</th><th>To</th><th>By</th><th>Reason</th><th>At</th></tr></thead><tbody>@foreach(($statusHistory ?? $a->statusHistory ?? []) as $h)<tr><td>{{ $h->previous_status ?? '—' }}</td><td><x-status-badge :status="$h->new_status ?? ''"/></td><td>{{ $h->changedBy->name ?? '—' }}</td><td>{{ $h->reason ?? '—' }}</td><td>{{ $h->created_at ?? '' }}</td></tr>@endforeach</tbody></table></div></div>@endif
</div></div></div>
<div class="tab-pane fade" id="aDocs"><div class="card shadow-sm"><div class="card-body">
@if(!empty($checklist))
<div class="d-flex justify-content-between align-items-center mb-2"><strong>Document Checklist</strong><span class="badge bg-info tnum">{{ $checklist['done'] }}/{{ $checklist['total'] }} ({{ $checklist['percent'] }}%)</span></div>
<div class="progress mb-3" style="height:8px"><div class="progress-bar" style="width:{{ $checklist['percent'] }}%"></div></div>
<div class="table-responsive mb-3"><table class="table table-sm"><thead><tr><th>Required Document</th><th>Status</th><th>Files</th></tr></thead>
<tbody>@foreach($checklist['items'] as $item)<tr><td>{{ $item['type'] }}</td><td><x-status-badge :status="$item['status']"/></td><td class="tnum">{{ $item['count'] }}</td></tr>@endforeach</tbody></table></div>
@endif
<div class="row">@forelse(($documents ?? $a->documents ?? []) as $d)<div class="col-md-4 mb-2">@include('documents._card',['document'=>$d])</div>@empty<p class="text-muted">No documents.</p>@endforelse</div><a href="{{ route('documents.create', ['application' => $a->id]) }}" class="btn btn-sm btn-primary mt-2">Upload Document</a></div></div></div>
<div class="tab-pane fade" id="aTime"><div class="card shadow-sm"><div class="card-body">@include('applications._timeline')</div></div></div>
<div class="tab-pane fade" id="aNotes"><div class="card shadow-sm"><div class="card-body">@include('notes._list',['notes'=>$notes ?? [],'notableType'=>'application','notableId'=>$a->id])</div></div></div>
<div class="tab-pane fade" id="aTasks"><div class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Title</th><th>Due</th><th>Status</th></tr></thead><tbody>@forelse(($tasks ?? $a->tasks ?? []) as $t)<tr><td>{{ $t->title }}</td><td>{{ $t->due_date ?? '—' }}</td><td><x-status-badge :status="$t->status ?? ''"/></td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No tasks.</td></tr>@endforelse</tbody></table></div><a href="{{ route('tasks.index', ['application' => $a->id]) }}" class="btn btn-sm btn-primary">Manage Tasks</a></div></div></div>
<div class="tab-pane fade" id="aJourney"><div class="row">
    <div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Offers</div><div class="card-body">@include('journey._offers',['offers'=>$offers ?? ($a->offer ? [$a->offer] : [])])</div></div></div>
    <div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">CAS</div><div class="card-body">@include('journey._cas',['casRecords'=>$casRecords ?? ($a->casRecord ? [$a->casRecord] : [])])</div></div></div>
    <div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Visa</div><div class="card-body">@include('journey._visa',['visaCases'=>$visaCases ?? $a->visaCases ?? []])</div></div></div>
    <div class="col-md-6 mb-3"><div class="card shadow-sm"><div class="card-header fw-semibold">Enrolment</div><div class="card-body">@include('journey._enrolment',['enrolments'=>$enrolments ?? ($a->enrolment ? [$a->enrolment] : [])])</div></div></div>
</div></div>
<div class="tab-pane fade" id="aFin"><div class="card shadow-sm"><div class="card-body"><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Type</th><th>Amount</th><th>Status</th></tr></thead><tbody>@forelse(($commissions ?? ($a->commission ? [$a->commission] : [])) as $f)<tr><td>Commission</td><td>£{{ number_format($f->amount ?? 0,2) }}</td><td><x-status-badge :status="$f->status ?? ''"/></td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No finance records.</td></tr>@endforelse</tbody></table></div><a href="{{ route('commissions.index', ['application' => $a->id]) }}" class="btn btn-sm btn-primary">View Finance</a></div></div></div>
</div>
@endsection
