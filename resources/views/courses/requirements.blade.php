@extends('layouts.app')
@section('title', 'Course Requirements')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Requirements: {{ $course->name }}</h4>
    <a href="{{ Route::has('courses.show') ? route('courses.show', $course) : url('/crm/courses/' . $course->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>Add Requirement</h6>
    <form method="POST" action="{{ Route::has('courses.requirements.store') ? route('courses.requirements.store', $course) : url('/crm/courses/' . $course->id . '/requirements') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label small" for="document_type_id">Document Type</label>
            <select name="document_type_id" id="document_type_id" class="form-select @error('document_type_id') is-invalid @enderror" required>
                <option value="">Select type</option>
                @foreach(($documentTypes ?? []) as $dt)
                    <option value="{{ $dt->id }}" @selected(old('document_type_id') == $dt->id)>{{ $dt->name }}</option>
                @endforeach
            </select>
            @error('document_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="required">Required</label>
            <select name="required" id="required" class="form-select">
                <option value="1" @selected(old('required', 1) == 1)>Yes</option>
                <option value="0" @selected(old('required') == 0)>No</option>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small" for="note">Note</label>
            <input type="text" name="note" id="note" value="{{ old('note') }}" class="form-control" maxlength="1000" placeholder="Optional note">
        </div>
        <div class="col-md-2"><button class="btn btn-primary btn-sm">Add</button></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>Document</th><th>Required</th><th>Note</th><th></th></tr></thead>
<tbody>
@forelse(($requirements ?? []) as $r)
<tr>
    <td>{{ $r->documentType->name ?? ('Type #' . $r->document_type_id) }}</td>
    <td>{{ $r->required ? 'Yes' : 'No' }}</td>
    <td class="small">{{ $r->note ?? '—' }}</td>
    <td>
        <form method="POST" action="{{ Route::has('courses.requirements.destroy') ? route('courses.requirements.destroy', [$course, $r]) : url('/crm/courses/' . $course->id . '/requirements/' . $r->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="4" class="text-center text-muted">No requirements yet. Add the first one above.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($requirements) && method_exists($requirements, 'links')){{ $requirements->links() }}@endif
</div></div>
@endsection
