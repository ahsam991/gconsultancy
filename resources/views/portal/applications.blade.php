@extends('layouts.app')
@section('title', 'My Applications')
@section('breadcrumb', 'Portal Applications')
@section('content')
<div class="container-fluid">
    <h1 class="h4">My applications</h1>
    @forelse(($applications ?? []) as $app)
        <div class="card mb-3"><div class="card-body">
            <h2 class="h6 mb-1">{{ is_array($app) ? ($app['title'] ?? 'Application') : ($app->title ?? 'Application') }}</h2>
            <p class="small">Status: <span class="badge bg-primary">{{ is_array($app) ? ($app['status'] ?? '') : ($app->status ?? '') }}</span></p>
            <ol class="small mb-0">
                @foreach(($app['history'] ?? (is_object($app) ? [] : [])) as $h)
                    <li>{{ is_array($h) ? ($h['status'] ?? '') : '' }} — {{ is_array($h) ? ($h['date'] ?? '') : '' }}</li>
                @endforeach
            </ol>
        </div></div>
    @empty
        <div class="alert alert-info">No applications yet. <a href="{{ url('/apply') }}">Apply online</a> to start.</div>
    @endforelse
</div>
@endsection
