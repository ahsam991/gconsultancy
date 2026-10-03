@extends('public.layout')
@section('title', 'Page not found (404)')
@section('content')
<div class="container py-5 text-center"><h1>404 — Page not found</h1><p class="lead">Sorry, that page doesn't exist or was moved.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
