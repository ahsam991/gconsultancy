@extends('public.layout')
@section('title', 'Compare Courses | Global Consultancy')
@section('meta_description', 'Compare up to three courses side by side by fee, duration, IELTS and university.')
@section('content')
<div class="container py-5">
    <h1>Compare Courses</h1>
    <p class="text-muted">Select up to three courses to compare side by side.</p>

    <form method="GET" action="{{ Route::has('engagement.compare') ? route('engagement.compare') : url('/engagement/compare') }}" class="row g-2 align-items-end mb-4">
        @csrf
        @for($i = 0; $i < 3; $i++)
        <div class="col-md-3">
            <label class="form-label small" for="ids_{{ $i }}">Course {{ $i + 1 }} ID</label>
            <input type="number" name="ids[]" id="ids_{{ $i }}" value="{{ old('ids.' . $i, $ids[$i] ?? '') }}" class="form-control" min="1">
        </div>
        @endfor
        <div class="col-md-3 d-flex gap-2">
            <button class="btn btn-primary">Compare</button>
            <a href="{{ Route::has('course-finder') ? route('course-finder') : url('/course-finder') }}" class="btn btn-outline-secondary">Finder</a>
        </div>
    </form>

    @if(isset($courses) && count($courses))
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th style="width:180px">Attribute</th>
                    @foreach($courses as $c)
                        <th>{{ $c->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr><th>University</th>@foreach($courses as $c)<td>{{ $c->university->name ?? '—' }}</td>@endforeach</tr>
                <tr><th>Country</th>@foreach($courses as $c)<td>{{ $c->university->country->name ?? '—' }}</td>@endforeach</tr>
                <tr><th>Tuition Fee</th>@foreach($courses as $c)<td>£{{ number_format($c->tuition_fee ?? 0, 2) }}</td>@endforeach</tr>
                <tr><th>Duration (months)</th>@foreach($courses as $c)<td>{{ $c->duration_months ?? '—' }}</td>@endforeach</tr>
                <tr><th>Study Mode</th>@foreach($courses as $c)<td>{{ $c->study_mode ?? '—' }}</td>@endforeach</tr>
                <tr><th>IELTS Required</th>@foreach($courses as $c)<td>{{ $c->ielts_required ?? '—' }}</td>@endforeach</tr>
                <tr><th>Intakes</th>@foreach($courses as $c)<td>{{ $c->intake_months ?? '—' }}</td>@endforeach</tr>
                <tr><th></th>@foreach($courses as $c)<td>
                    @if(Route::has('course.apply'))
                        <a href="{{ route('course.apply') }}?course_id={{ $c->id }}" class="btn btn-sm btn-success">Apply</a>
                    @else
                        <a href="{{ url('/course-apply?course_id=' . $c->id) }}" class="btn btn-sm btn-success">Apply</a>
                    @endif
                </td>@endforeach</tr>
            </tbody>
        </table>
    </div>
    @else
        <div class="alert alert-info">Enter up to three course IDs above to see a side-by-side comparison.</div>
    @endif
</div>
@endsection
