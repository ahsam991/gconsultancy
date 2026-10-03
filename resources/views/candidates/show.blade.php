@extends('layouts.app')
@section('title','Candidate Profile')
@section('content')
@php $c = $candidate; $pct = $docCompletion ?? 0; @endphp
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">{{ $c->first_name }} {{ $c->last_name }} <small class="text-muted">({{ $c->uid ?? $c->id }})</small> <x-status-badge :status="$c->status ?? 'new'"/></h4>
    <div class="d-flex gap-2">
        <a href="{{ route('candidates.edit', $c) }}" class="btn btn-sm btn-warning">Edit</a>
        <a href="{{ route('candidates.wizard', [$c, 1]) }}" class="btn btn-sm btn-outline-primary">Profile Wizard ({{ $c->profile_completion ?? 0 }}%)</a>
        <a href="{{ route('applications.create', ['candidate' => $c->id]) }}" class="btn btn-sm btn-primary">New Application</a>
        <a href="{{ route('candidates.index') }}" class="btn btn-sm btn-outline-secondary">Back</a>
    </div>
</div>
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tOver">Overview</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tAcad">Academics</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tDocs">Documents ({{ $pct }}%)</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tApps">Applications</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tTime">Timeline</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tTasks">Tasks &amp; Appointments</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tNotes">Notes</button></li>
</ul>
<div class="tab-content">
<div class="tab-pane fade show active" id="tOver"><div class="card shadow-sm"><div class="card-body row">
    <div class="col-md-6"><dl class="row mb-0">
    <dt class="col-4">Email</dt><dd class="col-8">{{ $c->email }}</dd>
    <dt class="col-4">Phone</dt><dd class="col-8">{{ $c->phone ?? '—' }}</dd>
    <dt class="col-4">DOB</dt><dd class="col-8">{{ isset($c->dob) ? \Carbon\Carbon::parse($c->dob)->format('d M Y') : '—' }}</dd>
    <dt class="col-4">Gender</dt><dd class="col-8">{{ $c->gender ?? '—' }}</dd>
    <dt class="col-4">Nationality</dt><dd class="col-8">{{ $c->nationality ?? '—' }}</dd>
    <dt class="col-4">Passport</dt><dd class="col-8">{{ $c->passport_number ?? '—' }}</dd>
    </dl></div>
    <div class="col-md-6"><dl class="row mb-0">
    <dt class="col-4">Address</dt><dd class="col-8">{{ $c->address ?? '—' }}</dd>
    <dt class="col-4">Destination</dt><dd class="col-8">{{ $c->destination ?? '—' }}</dd>
    <dt class="col-4">Level</dt><dd class="col-8">{{ $c->study_level ?? '—' }}</dd>
    <dt class="col-4">Subject</dt><dd class="col-8">{{ $c->subject ?? '—' }}</dd>
    <dt class="col-4">Source</dt><dd class="col-8">{{ $c->source ?? '—' }}</dd>
    <dt class="col-4">Assigned</dt><dd class="col-8">{{ $c->assignee->name ?? 'Unassigned' }}</dd>
    </dl></div>
</div></div></div>
<div class="tab-pane fade" id="tAcad"><div class="card shadow-sm"><div class="card-body">
    <div class="table-responsive"><table class="table table-striped"><thead><tr><th>Qualification</th><th>Institution</th><th>Year</th><th>Grade/GPA</th></tr></thead><tbody>
    @forelse(($academics ?? $c->academics ?? []) as $a)<tr><td>{{ $a->qualification ?? $a['qualification'] ?? '' }}</td><td>{{ $a->institution ?? $a['institution'] ?? '' }}</td><td>{{ $a->year ?? $a['year'] ?? '' }}</td><td>{{ $a->grade ?? $a['grade'] ?? '' }}</td></tr>@empty<tr><td colspan="4" class="text-center text-muted">No academic records.</td></tr>@endforelse
    </tbody></table></div>
    @if(isset($englishTests) || isset($c->english_tests))<h6 class="mt-3">English Tests</h6><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Test</th><th>Overall</th><th>Date</th></tr></thead><tbody>@forelse(($englishTests ?? []) as $t)<tr><td>{{ $t->type ?? '' }}</td><td>{{ $t->overall ?? '' }}</td><td>{{ $t->test_date ?? '' }}</td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No test records.</td></tr>@endforelse</tbody></table></div>@endif
</div></div></div>
<div class="tab-pane fade" id="tDocs"><div class="card shadow-sm"><div class="card-body">
    <div class="progress mb-3"><div class="progress-bar" style="width:{{ $pct }}%">{{ $pct }}%</div></div>
    <div class="row">@forelse(($documents ?? $c->documents ?? []) as $d)<div class="col-md-4 mb-2">@include('documents._card',['document'=>$d])</div>@empty<p class="text-muted">No documents uploaded.</p>@endforelse</div>
    <a href="{{ route('documents.create', ['candidate' => $c->id]) }}" class="btn btn-sm btn-primary mt-2">Upload Document</a>
</div></div></div>
<div class="tab-pane fade" id="tApps"><div class="card shadow-sm"><div class="card-body">
    <div class="table-responsive"><table class="table table-striped"><thead><tr><th>UID</th><th>University</th><th>Course</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse(($applications ?? $c->applications ?? []) as $a)<tr><td>{{ $a->uid ?? $a->id }}</td><td>{{ $a->university->name ?? '—' }}</td><td>{{ $a->course->name ?? '—' }}</td><td><x-status-badge :status="$a->status ?? ''"/></td><td><a href="{{ route('applications.show', $a) }}" class="btn btn-sm btn-outline-info">Open</a></td></tr>@empty<tr><td colspan="5" class="text-center text-muted">No applications yet.</td></tr>@endforelse
    </tbody></table></div>
</div></div></div>
<div class="tab-pane fade" id="tTime"><div class="card shadow-sm"><div class="card-body"><x-timeline :items="$timeline ?? []"/></div></div></div>
<div class="tab-pane fade" id="tTasks"><div class="row">
    <div class="col-md-6"><div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Tasks</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Title</th><th>Due</th><th>Status</th></tr></thead><tbody>@forelse(($tasks ?? []) as $t)<tr><td>{{ $t->title }}</td><td>{{ $t->due_date ?? '—' }}</td><td><x-status-badge :status="$t->status ?? ''"/></td></tr>@empty<tr><td colspan="3" class="text-center text-muted">No tasks.</td></tr>@endforelse</tbody></table></div></div></div></div>
    <div class="col-md-6"><div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Appointments</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Title</th><th>When</th></tr></thead><tbody>@forelse(($appointments ?? []) as $a)<tr><td>{{ $a->title }}</td><td>{{ $a->scheduled_at ?? '—' }}</td></tr>@empty<tr><td colspan="2" class="text-center text-muted">No appointments.</td></tr>@endforelse</tbody></table></div></div></div></div>
</div></div>
<div class="tab-pane fade" id="tNotes"><div class="card shadow-sm"><div class="card-body">@include('notes._list', ['notes' => $notes ?? [], 'notableType' => 'candidate', 'notableId' => $c->id])</div></div></div>
</div>
@endsection
