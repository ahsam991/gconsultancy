@extends('public.layout')

@section('title', 'About Us | Global Consultancy')
@section('meta_description', 'Who we are: experienced education counsellors guiding students to the UK, USA, Canada, Australia and Europe.')

@section('content')
<div class="container py-5">
    <h1>About Global Consultancy</h1>
    <p class="lead">We guide students from first counselling call to enrolment abroad — admissions, visas, accommodation and pre-departure.</p>
    <div class="row g-4 mt-2">
        <div class="col-12 col-md-6">
            <h2 class="h4">Our mission</h2>
            <p>Honest advice, transparent fees and end-to-end support so every student finds the right course, university and country.</p>
            <ul>
                <li>Free initial counselling and study-plan</li>
                <li>150+ partner universities worldwide</li>
                <li>96% visa success rate</li>
            </ul>
        </div>
        <div class="col-12 col-md-6">
            <h2 class="h4">Our team</h2>
            @forelse(($team ?? []) as $m)
                <p class="mb-1"><strong>{{ $m->name ?? $m['name'] ?? '' }}</strong> — {{ $m->role ?? $m['role'] ?? '' }}</p>
            @empty
                <p>Certified counsellors for UK, USA, Canada, Australia and Europe, plus a dedicated visa team.</p>
            @endforelse
            <a class="btn btn-primary mt-2" href="{{ url('/appointment') }}">Meet a counsellor</a>
        </div>
    </div>
</div>
@endsection
