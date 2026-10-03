@extends('layouts.app')
@section('title','Login History')
@section('content')
<h4 class="mb-3">Login History</h4>
<div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table mb-0">
<thead><tr><th>When</th><th>User / Email</th><th>Result</th><th>IP</th><th>Device</th><th>Browser</th></tr></thead>
<tbody>@forelse(($history ?? []) as $h)<tr><td class="tnum">{{ $h->created_at?->format('d M Y H:i') }}</td><td>{{ $h->user->name ?? $h->email ?? '—' }}</td><td><x-status-badge :status="$h->successful ? 'success' : 'failed'"/></td><td class="tnum">{{ $h->ip }}</td><td>{{ $h->device ?? '—' }}</td><td>{{ $h->browser ?? '—' }}</td></tr>@empty<tr><td colspan="6" class="text-muted p-3">No login records.</td></tr>@endforelse</tbody>
</table></div></div></div>
<div class="mt-2">{{ $history->links() }}</div>
@endsection
