@extends('layouts.app')
@section('title', 'University Scholarships')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Scholarships: {{ $university->name }}</h4>
    <a href="{{ Route::has('universities.show') ? route('universities.show', $university) : url('/crm/universities/' . $university->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>Add Scholarship</h6>
    <form method="POST" action="{{ Route::has('universities.scholarships.store') ? route('universities.scholarships.store', $university) : url('/crm/universities/' . $university->id . '/scholarships') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label small" for="title">Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" required maxlength="255">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="amount">Amount</label>
            <input type="number" step="0.01" min="0" name="amount" id="amount" value="{{ old('amount') }}" class="form-control @error('amount') is-invalid @enderror">
            @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="amount_type">Type</label>
            <select name="amount_type" id="amount_type" class="form-select">
                @foreach(['fixed' => 'Fixed', 'percentage' => 'Percentage', 'full' => 'Full', 'partial' => 'Partial'] as $val => $label)
                    <option value="{{ $val }}" @selected(old('amount_type', 'fixed') == $val)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="deadline">Deadline</label>
            <input type="date" name="deadline" id="deadline" value="{{ old('deadline') }}" class="form-control @error('deadline') is-invalid @enderror">
            @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="course_id">Course (optional)</label>
            <select name="course_id" id="course_id" class="form-select">
                <option value="">University-wide</option>
                @foreach(($courses ?? []) as $c)
                    <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-10">
            <label class="form-label small" for="criteria">Criteria</label>
            <input type="text" name="criteria" id="criteria" value="{{ old('criteria') }}" class="form-control" maxlength="2000" placeholder="Eligibility criteria">
        </div>
        <div class="col-md-2"><button class="btn btn-primary btn-sm">Add</button></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>Title</th><th>Amount</th><th>Course</th><th>Deadline</th><th>Active</th><th></th></tr></thead>
<tbody>
@forelse(($scholarships ?? []) as $s)
<tr>
    <td>{{ $s->title }}</td>
    <td>{{ $s->amount ?? '—' }} <span class="text-muted small">{{ $s->amount_type }}</span></td>
    <td class="small">{{ $s->course->name ?? 'University-wide' }}</td>
    <td class="small">{{ $s->deadline?->format('Y-m-d') ?? '—' }}</td>
    <td>{{ $s->active ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <form method="POST" action="{{ Route::has('universities.scholarships.update') ? route('universities.scholarships.update', [$university, $s]) : url('/crm/universities/' . $university->id . '/scholarships/' . $s->id) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="title" value="{{ $s->title }}">
            <input type="hidden" name="active" value="{{ $s->active ? 0 : 1 }}">
            <button class="btn btn-sm btn-outline-secondary">{{ $s->active ? 'Hide' : 'Show' }}</button>
        </form>
        <form method="POST" action="{{ Route::has('universities.scholarships.destroy') ? route('universities.scholarships.destroy', [$university, $s]) : url('/crm/universities/' . $university->id . '/scholarships/' . $s->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No scholarships yet. Add the first one above.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($scholarships) && method_exists($scholarships, 'links')){{ $scholarships->links() }}@endif
</div></div>
@endsection
