@extends('layouts.app')
@section('title','Courses')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Courses</h4><div class="d-flex gap-2"><a href="{{ route('courses.finder') }}" class="btn btn-sm btn-outline-primary">Course Finder</a><a href="{{ route('courses.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Course</a></div></div>
<x-filter-panel>
<form method="GET" action="{{ route('courses.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-2"><label class="form-label small">University</label><select name="university_id" class="form-select"><option value="">All</option>@foreach(($universities ?? []) as $u)<option value="{{ $u->id }}" @selected(request('university_id')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Level</label><select name="level" class="form-select"><option value="">All</option>@foreach(['Foundation','Undergraduate','Postgraduate','Diploma','PhD'] as $l)<option @selected(request('level')==$l)>{{ $l }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Subject</label><input name="subject" value="{{ request('subject') }}" class="form-control" placeholder="e.g. Business"></div>
    <div class="col-md-2"><label class="form-label small">Country</label><select name="country" class="form-select"><option value="">All</option>@foreach(['UK','Canada','Australia','USA','New Zealand','Ireland'] as $ct)<option @selected(request('country')==$ct)>{{ $ct }}</option>@endforeach</select></div>
    <div class="col-md-2"><label class="form-label small">Max Fee (£)</label><input type="number" name="max_fee" value="{{ request('max_fee') }}" class="form-control"></div>
    <div class="col-md-2 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="coursesTable">
<thead><tr><th>Name</th><th>University</th><th>Level</th><th>Subject</th><th>Fee</th><th>Actions</th></tr></thead>
<tbody>@forelse(($courses ?? []) as $c)<tr>
<td><a href="{{ route('courses.show', $c) }}">{{ $c->name }}</a></td><td>{{ $c->university->name ?? '—' }}</td><td>{{ $c->level ?? '—' }}</td><td>{{ $c->subject ?? '—' }}</td><td>£{{ number_format($c->tuition_fee ?? 0,2) }}</td>
<td class="text-nowrap"><a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-info">View</a><a href="{{ route('courses.edit', $c) }}" class="btn btn-sm btn-outline-warning">Edit</a><form method="POST" action="{{ route('courses.destroy', $c) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
