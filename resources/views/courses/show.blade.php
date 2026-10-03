@extends('layouts.app')
@section('title','Course Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">{{ $course->name }}</h4><div class="d-flex gap-2"><a href="{{ route('applications.create', ['course' => $course->id, 'university' => $course->university_id]) }}" class="btn btn-sm btn-success">Apply</a><a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('courses.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div></div>
<div class="card shadow-sm"><div class="card-body row">
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">University</dt><dd class="col-8"><a href="{{ route('universities.show', $course->university_id) }}">{{ $course->university->name ?? '—' }}</a></dd><dt class="col-4">Level</dt><dd class="col-8">{{ $course->level ?? '—' }}</dd><dt class="col-4">Subject</dt><dd class="col-8">{{ $course->subject ?? '—' }}</dd><dt class="col-4">Duration</dt><dd class="col-8">{{ $course->duration ?? '—' }}</dd></dl></div>
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Tuition Fee</dt><dd class="col-8">£{{ number_format($course->tuition_fee ?? 0,2) }}</dd><dt class="col-4">Deposit</dt><dd class="col-8">£{{ number_format($course->deposit ?? 0,2) }}</dd><dt class="col-4">Intakes</dt><dd class="col-8">{{ is_array($course->intakes ?? null) ? implode(', ', $course->intakes) : ($course->intakes ?? '—') }}</dd></dl></div>
<div class="col-12 mt-3"><h6>Entry Requirements</h6><p>{{ $course->requirements ?? '—' }}</p></div>
</div></div>
@endsection
