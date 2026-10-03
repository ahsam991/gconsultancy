@extends('public.layout')

@section('title', 'Course Finder | Global Consultancy')
@section('meta_description', 'Filter courses by subject, level, country, university, intake, fees and IELTS.')

@section('content')
<div class="container py-5">
    <h1>Course finder</h1>
    <form action="{{ url('/course-finder') }}" method="GET" class="card card-body mb-4">
        <div class="row g-2">
            <div class="col-12 col-md-4"><label class="form-label" for="f-q">Subject / keyword</label><input id="f-q" name="q" value="{{ request('q') }}" class="form-control" placeholder="e.g. Data Science"></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-level">Level</label><select id="f-level" name="level" class="form-select"><option value="">Any</option>@foreach(['UG','PG','Foundation','Diploma','PhD'] as $l)<option {{ request('level')==$l?'selected':'' }}>{{ $l }}</option>@endforeach</select></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-country">Country</label><select id="f-country" name="country" class="form-select"><option value="">Any</option>@foreach(['UK','USA','Canada','Australia','Europe'] as $c)<option {{ request('country')==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-uni">University</label><input id="f-uni" name="university" value="{{ request('university') }}" class="form-control" placeholder="Any"></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-intake">Intake</label><select id="f-intake" name="intake" class="form-select"><option value="">Any</option>@foreach(['September','January','May'] as $i)<option {{ request('intake')==$i?'selected':'' }}>{{ $i }}</option>@endforeach</select></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-maxfee">Max fee</label><input id="f-maxfee" name="max_fee" value="{{ request('max_fee') }}" class="form-control" inputmode="numeric" placeholder="e.g. 20000"></div>
            <div class="col-6 col-md-2"><label class="form-label" for="f-ielts">Min IELTS</label><input id="f-ielts" name="min_ielts" value="{{ request('min_ielts') }}" class="form-control" inputmode="decimal" placeholder="e.g. 6.0"></div>
            <div class="col-12 col-md-2 d-grid align-self-end"><button class="btn btn-primary" type="submit">Search</button></div>
        </div>
    </form>
    <p class="text-muted">{{ isset($courses) ? count($courses) : 0 }} results</p>
    <div class="row g-3">
        @forelse(($courses ?? []) as $course)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <h2 class="h5">{{ $course->title ?? $course['title'] ?? '' }}</h2>
                    <p class="small text-muted">{{ $course->level ?? $course['level'] ?? '' }}</p>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/courses/' . ($course->id ?? $course['id'] ?? '')) }}">View</a>
                    <a class="btn btn-sm btn-primary" href="{{ url('/apply') }}">Apply</a>
                </div></div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No results. Widen your filters or <a href="{{ url('/contact') }}">ask a counsellor</a>.</p></div>
        @endforelse
    </div>
</div>
@endsection
