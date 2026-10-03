@extends('public.layout')

@section('title', 'Our Services | Global Consultancy')
@section('meta_description', 'Counselling, admissions, visa support, accommodation, pre-departure and post-arrival services for international students.')

@section('content')
<div class="container py-5">
    <h1>Our services</h1>
    <p class="lead">Everything you need to study abroad — free at the counselling stage.</p>
    <div class="row g-3 mt-2">
        @foreach([
            ['Counselling', 'Country, course and university selection matched to grades, budget and goals.'],
            ['Admission', 'Applications, SOP/LOR review, deadlines and offer management.'],
            ['Visa support', 'Financial documents, CAS guidance, mock interviews and file checking.'],
            ['Accommodation', 'University halls and verified private housing near campus.'],
            ['Pre-departure', 'Flights, forex, insurance, packing and enrolment briefing.'],
            ['Post-arrival', 'Airport pickup guidance, bank account, part-time work and stay-back advice.'],
        ] as [$title, $desc])
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body"><h2 class="h5">{{ $title }}</h2><p class="small">{{ $desc }}</p></div></div>
            </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex gap-2 flex-wrap">
        <a class="btn btn-primary btn-lg" href="{{ url('/appointment') }}">Book free counselling</a>
        <a class="btn btn-outline-primary btn-lg" href="{{ url('/apply') }}">Apply online</a>
    </div>
</div>
@endsection
