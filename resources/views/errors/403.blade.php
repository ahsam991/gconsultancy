@extends('public.layout')
@section('title', 'Forbidden (403)')
@section('content')
<div class="container py-5 text-center"><h1>403 — Forbidden</h1><p class="lead">You don't have permission to view this page.</p><a class="btn btn-primary" href="{{ url('/') }}">Go home</a></div>
@endsection
