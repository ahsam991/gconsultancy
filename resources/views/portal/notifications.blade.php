@extends('layouts.app')
@section('title','Notifications')
@section('content')
<div class="container-fluid">
<h1 class="h4">Notifications</h1>
<div class="list-group">
@forelse(($notifications ?? []) as $n)
<div class="list-group-item d-flex justify-content-between align-items-start {{ empty($n->read_at) ? 'list-group-item-primary' : '' }}">
<div><div class="fw-semibold">{{ $n->data['title'] ?? 'Notification' }}</div><div class="small">{{ $n->data['message'] ?? '' }}</div><small class="text-muted">{{ $n->created_at ?? '' }}</small></div>
@if(empty($n->read_at))<form method="POST" action="{{ route('portal.notifications.read', $n->id) }}">@csrf<button class="btn btn-sm btn-outline-primary">Mark read</button></form>@endif
</div>
@empty<div class="list-group-item"><x-empty-state title="No notifications" message="You are all caught up." /></div>@endforelse
</div>
</div>
@endsection
