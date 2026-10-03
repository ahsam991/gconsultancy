@extends('public.layout')

@section('title', ($course->title ?? $course['title'] ?? 'Course') . ' | Global Consultancy')
@section('meta_description', 'Entry requirements, fees, intake and how to apply.')

@section('content')
<div class="container py-5">
    @php($c = $course ?? ['title' => 'Course'])
    <h1>{{ is_array($c) ? ($c['title'] ?? 'Course') : ($c->title ?? 'Course') }}</h1>
    <p class="text-muted">{{ is_array($c) ? ($c['level'] ?? '') : ($c->level ?? '') }} · {{ is_array($c) ? ($c['university'] ?? '') : ($c->university ?? '') }}</p>
    <h2 class="h4 mt-3">Entry requirements</h2>
    <p>{{ is_array($c) ? ($c['requirements'] ?? 'Contact us for detailed entry requirements (grades, IELTS/PTE, documents).') : ($c->requirements ?? '') }}</p>
    <h2 class="h4 mt-3">Fees &amp; intake</h2>
    <p>Annual fee: {{ is_array($c) ? ($c['fee'] ?? 'On request') : ($c->fee ?? 'On request') }} · Intake: {{ is_array($c) ? ($c['intake'] ?? 'September / January') : ($c->intake ?? '') }}</p>
    <div class="mt-3 d-flex gap-2 flex-wrap">
        <a class="btn btn-primary btn-lg" href="{{ url('/apply') }}">Apply now</a>
        <a class="btn btn-outline-primary btn-lg" href="{{ url('/contact') }}">Enquire</a>
    </div>
</div>
@endsection
