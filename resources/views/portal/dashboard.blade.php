@extends('layouts.app')
@section('title', 'My Dashboard')
@section('breadcrumb', 'Portal Dashboard')
@section('content')
<div class="container-fluid">
    <span class="gc-eyebrow blue">Candidate portal</span>
    <h1 class="gc-display">Good day, {{ auth()->user()->name ?? 'Student' }}</h1>

    @php
        $steps = config('consultancy.journey_steps', ['profile','application','offer','deposit','cas','visa','enrolment']);
        $progress = (int) ($progress ?? 15);
        $profileCompletion = (int) ($profileCompletion ?? 40);
        $docStats = $docStats ?? ['required' => 6, 'uploaded' => 0];
        $applications = $applications ?? [];
        $offers = $offers ?? [];
        $visaStatus = $visaStatus ?? 'Not started';
        $appointments = $appointments ?? [];
        $tasks = $tasks ?? [];
        $counsellor = $counsellor ?? ['name' => 'Your counsellor', 'phone' => '+880-1XXX-XXXXXX'];
        $doneUntil = (int) floor($progress / 100 * count($steps));
    @endphp

    <div class="gc-track my-3">
        <div class="d-flex justify-content-between align-items-center">
            <div><span class="gc-eyebrow">Application strength</span><div class="pct tnum">{{ $progress }}%</div></div>
            <div class="text-end small text-muted">Profile {{ $profileCompletion }}% · Docs {{ $docStats['uploaded'] ?? 0 }}/{{ $docStats['required'] ?? 0 }}</div>
        </div>
        <div class="progress my-2" role="progressbar" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100" aria-label="Journey progress"><div class="progress-bar" style="width: {{ $progress }}%"></div></div>
        <div class="gc-rail mt-2">
            @foreach($steps as $i => $step)
            <div class="stop {{ $i < $doneUntil ? 'done' : ($i === $doneUntil ? 'now' : '') }}"><div class="dot"></div>{{ ucfirst($step) }}</div>
            @endforeach
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">Profile completion — {{ $profileCompletion }}%</h2>
                <div class="progress mb-2"><div class="progress-bar bg-info" style="width: {{ $profileCompletion }}%"></div></div>
                <a class="btn btn-outline-primary w-100" href="{{ url('/candidate/profile') }}">Complete profile</a>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">Documents — {{ $docStats['uploaded'] ?? 0 }}/{{ $docStats['required'] ?? 6 }} uploaded</h2>
                <a class="btn btn-outline-primary w-100" href="{{ url('/candidate/documents') }}">Upload documents</a>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">My applications</h2>
                @forelse($applications as $app)
                    <p class="small mb-1">{{ is_array($app) ? ($app['title'] ?? '') : ($app->title ?? '') }} — <span class="badge bg-primary">{{ is_array($app) ? ($app['status'] ?? '') : ($app->status ?? '') }}</span></p>
                @empty
                    <p class="small text-muted">No applications yet.</p>
                @endforelse
                <a class="btn btn-outline-primary w-100" href="{{ url('/candidate/applications') }}">View applications</a>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">Offers &amp; visa</h2>
                <p class="small mb-1">Offers: {{ is_countable($offers) ? count($offers) : 0 }}</p>
                <p class="small">Visa status: <span class="badge bg-secondary">{{ $visaStatus }}</span></p>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">Appointments</h2>
                @forelse($appointments as $a)
                    <p class="small mb-1">{{ is_array($a) ? ($a['date'] ?? '') : ($a->date ?? '') }}</p>
                @empty
                    <p class="small text-muted">No upcoming appointments.</p>
                @endforelse
                <a class="btn btn-outline-primary w-100" href="{{ url('/candidate/appointments') }}">Manage appointments</a>
            </div></div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card h-100"><div class="card-body">
                <h2 class="h6">Tasks</h2>
                @forelse($tasks as $t)
                    <p class="small mb-1">{{ is_array($t) ? ($t['title'] ?? '') : ($t->title ?? '') }}</p>
                @empty
                    <p class="small text-muted">No pending tasks.</p>
                @endforelse
            </div></div>
        </div>
        <div class="col-12">
            <div class="card"><div class="card-body">
                <h2 class="h6">My counsellor</h2>
                <p class="mb-0">{{ is_array($counsellor) ? ($counsellor['name'] ?? '') : ($counsellor->name ?? '') }} · {{ is_array($counsellor) ? ($counsellor['phone'] ?? '') : ($counsellor->phone ?? '') }}</p>
            </div></div>
        </div>
    </div>
</div>
@endsection
