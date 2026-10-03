@extends('layouts.app')
@section('title', 'My Appointments')
@section('breadcrumb', 'Portal Appointments')
@section('content')
<div class="container-fluid">
    <h1 class="h4">My appointments</h1>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <h2 class="h6">Upcoming</h2>
    @forelse(($appointments ?? []) as $a)
        <div class="card mb-2"><div class="card-body py-2 d-flex justify-content-between align-items-center"><div><span class="fw-semibold">{{ $a->appointment_date?->format('d M Y, H:i') ?? $a->appointment_date }}</span><br><small class="text-muted">{{ $a->type ?? '' }} · {{ $a->location ?? $a->meeting_url ?? '' }}</small></div><x-status-badge :status="$a->status ?? ''"/></div></div>
    @empty
        <p class="text-muted">No upcoming appointments.</p>
    @endforelse
    @if(method_exists($appointments ?? null, 'links'))<div class="mt-2">{{ $appointments->links() }}</div>@endif
    <div class="card card-body mt-3">
        <h2 class="h6">Request a new appointment</h2>
        <form action="{{ route('portal.appointments.store') }}" method="POST">
            @csrf
            <div class="row g-2">
                <div class="col-6"><label class="form-label" for="pa-date">Date</label><input id="pa-date" name="date" type="date" min="{{ date('Y-m-d') }}" class="form-control form-control-lg" required></div>
                <div class="col-6"><label class="form-label" for="pa-time">Time</label><input id="pa-time" name="time" type="time" class="form-control form-control-lg" required></div>
                <div class="col-12"><label class="form-label" for="pa-type">Type</label><select id="pa-type" name="type" class="form-select form-select-lg"><option>Counselling</option><option>Visa</option><option>Pre-departure</option><option>Document</option><option>Follow-up</option></select></div>
                <div class="col-12"><label class="form-label" for="pa-notes">Notes</label><textarea id="pa-notes" name="notes" rows="2" class="form-control form-control-lg"></textarea></div>
            </div>
            <button class="btn btn-primary btn-lg w-100 mt-3" type="submit">Request appointment</button>
        </form>
    </div>
</div>
@endsection
