@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Notifications</h4><form method="POST" action="{{ route('notifications.read-all') }}">@csrf<button class="btn btn-sm btn-outline-primary">Mark all read</button></form></div>
<div class="list-group">
@forelse(($notifications ?? []) as $n)
<div class="list-group-item d-flex justify-content-between align-items-start {{ empty($n->read_at) ? 'list-group-item-primary' : '' }}">
<div><div class="fw-semibold">{{ $n->data['title'] ?? $n->title ?? 'Notification' }}</div><div class="small">{{ $n->data['message'] ?? $n->body ?? '' }}</div><small class="text-muted">{{ $n->created_at ?? '' }}</small></div>
@if(empty($n->read_at))<form method="POST" action="{{ route('notifications.read', $n) }}">@csrf<button class="btn btn-sm btn-outline-secondary">Mark read</button></form>@endif
</div>
@empty
<div class="list-group-item text-muted">No notifications.</div>
@endforelse
</div>
@endsection
