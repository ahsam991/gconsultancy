@extends('public.layout')
@section('title', 'Server error (500)')
@section('content')
<div class="container py-5 text-center"><h1>500 — Something went wrong</h1><p class="lead">We're working on it. Please try again later.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
