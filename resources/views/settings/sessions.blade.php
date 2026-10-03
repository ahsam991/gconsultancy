@extends('layouts.app')
@section('title','Active Sessions')
@section('content')
<h4 class="mb-3">Active Sessions <small class="text-muted">(database driver)</small></h4>
<div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>User</th><th>IP</th><th>Last Activity</th><th>Actions</th></tr></thead>
<tbody>@forelse(($sessions ?? []) as $s)<tr><td>{{ ($users[$s->user_id] ?? null)?->name ?? 'Guest' }}</td><td class="tnum">{{ $s->ip_address }}</td><td class="tnum">{{ \Carbon\Carbon::createFromTimestamp($s->last_activity)->diffForHumans() }}</td>
<td><form method="POST" action="{{ route('settings.sessions.terminate', $s->id) }}" class="d-inline" onsubmit="return confirm('Force logout this session?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Force logout</button></form></td></tr>@empty<tr><td colspan="4" class="text-muted p-3">No active sessions.</td></tr>@endforelse</tbody>
</table></div></div></div>
<div class="mt-2">{{ $sessions->links() }}</div>
@endsection
