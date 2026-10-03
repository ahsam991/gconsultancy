@extends('layouts.app')
@section('title', 'My Wishlist')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Saved Courses</h4>
    <a href="{{ Route::has('courses.finder') ? route('courses.finder') : url('/crm/courses-finder') }}" class="btn btn-sm btn-outline-primary">Find Courses</a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <form method="POST" action="{{ Route::has('engagement.wishlist.store') ? route('engagement.wishlist.store') : url('/crm/engagement/wishlist') }}" class="row g-2 align-items-end">
            @csrf
            <div class="col-md-4">
                <label class="form-label small" for="candidate_id">Candidate</label>
                <select name="candidate_id" id="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror">
                    <option value="">Select candidate</option>
                    @foreach(($candidates ?? []) as $c)
                        <option value="{{ $c->id }}" @selected(old('candidate_id', $candidateId ?? '') == $c->id)>{{ $c->first_name }} {{ $c->last_name }} — {{ $c->email }}</option>
                    @endforeach
                </select>
                @error('candidate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label small" for="course_id">Course ID</label>
                <input type="number" name="course_id" id="course_id" value="{{ old('course_id') }}" class="form-control @error('course_id') is-invalid @enderror" placeholder="e.g. 12" required>
                @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-primary">Save Course</button>
                <button class="btn btn-outline-secondary" formaction="{{ Route::has('engagement.wishlist.toggle') ? route('engagement.wishlist.toggle') : url('/crm/engagement/wishlist/toggle') }}">Toggle</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Course</th><th>University</th><th>Fee</th><th>Saved</th><th></th></tr></thead>
                <tbody>
                @forelse(($saved ?? []) as $s)
                    <tr>
                        <td>{{ $s->course->name ?? '—' }}</td>
                        <td>{{ $s->course->university->name ?? '—' }}</td>
                        <td>£{{ number_format($s->course->tuition_fee ?? 0, 2) }}</td>
                        <td class="small text-muted">{{ $s->created_at?->format('Y-m-d') }}</td>
                        <td class="text-nowrap">
                            @if(Route::has('courses.show'))
                                <a href="{{ route('courses.show', $s->course_id) }}" class="btn btn-sm btn-outline-info">View</a>
                            @else
                                <a href="{{ url('/crm/courses/' . $s->course_id) }}" class="btn btn-sm btn-outline-info">View</a>
                            @endif
                            <form method="POST" action="{{ Route::has('engagement.wishlist.destroy') ? route('engagement.wishlist.destroy', $s->id) : url('/crm/engagement/wishlist/' . $s->id) }}" class="d-inline" onsubmit="return confirm('Remove?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No saved courses yet. Use the form above to save one.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($saved) && method_exists($saved, 'links')){{ $saved->withQueryString()->links() }}@endif
    </div>
</div>
@endsection
