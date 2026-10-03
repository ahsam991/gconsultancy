@extends('layouts.app')
@section('title','University Detail')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">{{ $university->name }} <small class="text-muted">{{ $university->country }}</small></h4><div class="d-flex gap-2"><a href="{{ route('universities.edit', $university) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('universities.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div></div>
<div class="card shadow-sm mb-3"><div class="card-body row">
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">City</dt><dd class="col-8">{{ $university->city ?? '—' }}</dd><dt class="col-4">Ranking</dt><dd class="col-8">{{ $university->ranking ?? '—' }}</dd><dt class="col-4">Partner</dt><dd class="col-8">{{ ($university->is_partner ?? false) ? 'Yes' : 'No' }}</dd></dl></div>
<div class="col-md-6"><dl class="row mb-0"><dt class="col-4">Commission</dt><dd class="col-8">{{ $university->commission_rate ?? '—' }}%</dd><dt class="col-4">Website</dt><dd class="col-8"><a href="{{ $university->website ?? '#' }}" target="_blank">{{ $university->website ?? '—' }}</a></dd><dt class="col-4">Campuses</dt><dd class="col-8">@forelse(($university->campuses ?? []) as $cp){{ $cp->name ?? $cp }}@if(!$loop->last), @endif @empty — @endforelse</dd></dl></div>
<div class="col-12 mt-2"><p class="mb-0">{{ $university->description ?? '' }}</p></div>
</div></div>
<div class="card shadow-sm"><div class="card-header d-flex justify-content-between"><span class="fw-semibold">Courses</span><a href="{{ route('courses.create', ['university' => $university->id]) }}" class="btn btn-sm btn-primary">Add Course</a></div>
<div class="card-body"><div class="table-responsive"><table class="table table-striped"><thead><tr><th>Name</th><th>Level</th><th>Fee</th><th></th></tr></thead><tbody>
@forelse(($university->courses ?? $courses ?? []) as $c)<tr><td><a href="{{ route('courses.show', $c) }}">{{ $c->name }}</a></td><td>{{ $c->level ?? '—' }}</td><td>£{{ number_format($c->tuition_fee ?? 0,2) }}</td><td><a href="{{ route('courses.show', $c) }}" class="btn btn-sm btn-outline-info">View</a></td></tr>@empty<tr><td colspan="4" class="text-center text-muted">No courses.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
