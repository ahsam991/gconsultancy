@extends('layouts.app')
@section('title','My Tasks')
@section('content')
<div class="container-fluid">
<h1 class="h4">My Tasks</h1>
<div class="card shadow-sm"><div class="card-body p-0">
<div class="table-responsive"><table class="table table-hover mb-0">
<thead><tr><th>Task</th><th>Priority</th><th>Status</th><th>Due</th></tr></thead>
<tbody>@forelse(($tasks ?? []) as $t)<tr><td>{{ $t->title }}@if($t->description)<br><small class="text-muted">{{ $t->description }}</small>@endif</td><td><x-status-badge :status="$t->priority ?? ''"/></td><td><x-status-badge :status="$t->status ?? ''"/></td><td>{{ $t->due_date ?? '—' }}</td></tr>@empty<tr><td colspan="4"><x-empty-state title="No tasks" message="Your counsellor has not assigned any tasks yet." /></td></tr>@endforelse</tbody>
</table></div>
</div></div>
@if(method_exists($tasks ?? null, 'links'))<div class="mt-2">{{ $tasks->links() }}</div>@endif
</div>
@endsection
