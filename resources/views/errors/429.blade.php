@extends('public.layout')
@section('title', 'Too many requests (429)')
@section('content')
<div class="container py-5 text-center"><h1>429 — Too many requests</h1><p class="lead">Please wait a moment and try again.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
