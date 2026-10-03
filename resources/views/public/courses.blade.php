@extends('public.layout')

@section('title', 'Courses | Global Consultancy')
@section('meta_description', 'Search undergraduate and postgraduate courses abroad by subject, level, country and budget.')

@section('content')
<div class="container py-5">
    <h1>Courses</h1>
    <p class="lead">{{ ($total ?? (isset($courses) ? count($courses) : 0)) }} courses found.</p>
    <div class="row g-3">
        @forelse(($courses ?? []) as $course)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <h2 class="h5">{{ $course->title ?? $course['title'] ?? '' }}</h2>
                    <p class="small text-muted">{{ $course->level ?? $course['level'] ?? '' }}</p>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/courses/' . ($course->id ?? $course['id'] ?? '')) }}">View details</a>
                </div></div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No courses found. <a href="{{ url('/course-finder') }}">Try the course finder</a>.</p></div>
        @endforelse
    </div>
</div>
@endsection
