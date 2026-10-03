@extends('layouts.app')
@section('title','User')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<h4 class="mb-0">{{ $user->name }}</h4>
<div class="d-flex gap-2"><a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
</div>
<div class="card shadow-sm"><div class="card-body row">
<div class="col-md-6"><dl class="row mb-0">
<dt class="col-4">Email</dt><dd class="col-8">{{ $user->email }}</dd>
<dt class="col-4">Role</dt><dd class="col-8">{{ ucfirst($user->role->name ?? '—') }}</dd>
<dt class="col-4">Team</dt><dd class="col-8">@if($user->team)<a href="{{ route('teams.show', $user->team) }}">{{ $user->team->name }}</a>@else — @endif</dd>
</dl></div>
<div class="col-md-6"><dl class="row mb-0">
<dt class="col-4">Phone</dt><dd class="col-8">{{ $user->phone ?? '—' }}</dd>
<dt class="col-4">Status</dt><dd class="col-8"><x-status-badge :status="$user->status ?? 'active'"/></dd>
<dt class="col-4">Joined</dt><dd class="col-8">{{ $user->created_at?->format('d M Y') ?? '' }}</dd>
</dl></div>
</div></div>
@endsection
