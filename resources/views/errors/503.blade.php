@extends('public.layout')
@section('title', 'Service unavailable (503)')
@section('content')
<div class="container py-5 text-center"><h1>503 — Be right back</h1><p class="lead">We're doing maintenance. Please check back soon.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
