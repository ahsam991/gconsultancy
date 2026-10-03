@extends('layouts.app')
@section('title','Edit Counselling Session')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Counselling Session — {{ ($session->candidate->first_name ?? '') }} {{ ($session->candidate->last_name ?? '') }}</h4>
    @if(Route::has('counselling.index'))
    <a href="{{ route('counselling.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
    @endif
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<p class="mb-1 small text-muted">Recorded by {{ $session->staff->name ?? '—' }} on {{ $session->created_at ? $session->created_at->format('d M Y H:i') : '—' }}</p>
</div></div>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('counselling.update') ? route('counselling.update', $session) : url('/counselling/'.$session->id) }}">
    @csrf
    @method('PUT')
    @include('counselling._form', ['session' => $session])
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Update Session</button>
        @if(Route::has('counselling.index'))
        <a href="{{ route('counselling.index') }}" class="btn btn-outline-secondary">Cancel</a>
        @endif
    </div>
</form>
</div></div>
@endsection
