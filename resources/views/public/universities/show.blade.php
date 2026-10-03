@extends('public.layout')

@section('title', ($university->name ?? $university['name'] ?? 'University') . ' | Global Consultancy')
@section('meta_description', 'Admissions, courses, fees and scholarships.')

@section('content')
<div class="container py-5">
    @php($u = $university ?? ['name' => 'University'])
    <h1>{{ is_array($u) ? ($u['name'] ?? 'University') : ($u->name ?? 'University') }}</h1>
    <p class="text-muted">{{ is_array($u) ? ($u['country'] ?? '') : ($u->country ?? '') }}</p>
    <p>{{ is_array($u) ? ($u['description'] ?? 'Partner university. Contact us for courses, fees and scholarships.') : ($u->description ?? '') }}</p>
    <h2 class="h4 mt-4">Courses</h2>
    <ul>
        @forelse(($courses ?? (is_object($u) && method_exists($u, 'relationLoaded') ? [] : [])) as $course)
            <li><a href="{{ url('/courses/' . ($course->id ?? $course['id'] ?? '')) }}">{{ $course->title ?? $course['title'] ?? '' }}</a></li>
        @empty
            <li class="text-muted">Course list available on request — <a href="{{ url('/course-finder') }}">search the course finder</a>.</li>
        @endforelse
    </ul>
    <div class="mt-3 d-flex gap-2 flex-wrap">
        <a class="btn btn-primary" href="{{ url('/apply') }}">Apply now</a>
        <a class="btn btn-outline-primary" href="{{ url('/contact') }}">Enquire</a>
    </div>
</div>
@endsection
