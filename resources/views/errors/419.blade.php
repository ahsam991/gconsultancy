@extends('public.layout')
@section('title', 'Session expired (419)')
@section('content')
<div class="container py-5 text-center"><h1>419 — Session expired</h1><p class="lead">Your session expired. Please try again.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
