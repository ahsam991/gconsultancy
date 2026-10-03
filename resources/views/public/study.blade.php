@extends('public.layout')

@section('title', 'Study in {{ ucfirst($destination ?? request()->route("destination") ?? "Abroad") }} | Global Consultancy')
@section('meta_description', 'Universities, courses, costs and visa guidance for your chosen study destination.')

@section('content')
<div class="container py-5">
    @php($dest = $destination ?? ['name' => ucfirst(request()->route('destination') ?? 'Abroad'), 'description' => null])
    <h1>Study in {{ is_array($dest) ? ($dest['name'] ?? 'Abroad') : ($dest->name ?? 'Abroad') }}</h1>
    <p class="lead">{{ is_array($dest) ? ($dest['description'] ?? 'Top universities, intakes, costs and visa steps — with counsellor support.') : ($dest->description ?? 'Top universities, intakes, costs and visa steps.') }}</p>

    <h2 class="h4 mt-4">Universities</h2>
    <div class="row g-3">
        @forelse(($universities ?? []) as $uni)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h5">{{ $uni->name ?? $uni['name'] ?? '' }}</h3>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/universities/' . ($uni->id ?? $uni['id'] ?? '')) }}">View</a>
                </div></div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">University list for this destination is being updated — <a href="{{ url('/contact') }}">ask a counsellor</a>.</p></div>
        @endforelse
    </div>

    <h2 class="h4 mt-4">Popular courses</h2>
    <div class="row g-3">
        @forelse(($courses ?? []) as $course)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <h3 class="h5">{{ $course->title ?? $course['title'] ?? '' }}</h3>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/courses/' . ($course->id ?? $course['id'] ?? '')) }}">View course</a>
                </div></div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">Course list for this destination is being updated — <a href="{{ url('/course-finder') }}">try the course finder</a>.</p></div>
        @endforelse
    </div>

    <div class="mt-4 d-flex gap-2 flex-wrap">
        <a class="btn btn-primary" href="{{ url('/appointment') }}">Book counselling</a>
        <a class="btn btn-outline-primary" href="{{ url('/apply') }}">Apply online</a>
    </div>
</div>
@endsection
