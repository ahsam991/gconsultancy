@extends('layouts.app')
@section('title','Queue Monitor')
@section('content')
<h4 class="mb-3">Queue Monitor</h4>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">Pending Jobs ({{ is_countable($jobs ?? null) ? count($jobs) : ($jobs->total() ?? 0) }})</div><div class="card-body">
<x-datatable id="jobsTable">
<thead><tr><th>ID</th><th>Queue</th><th>Attempts</th><th>Available</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($jobs ?? []) as $j)
<tr><td class="tnum">{{ $j->id }}</td><td>{{ $j->queue }}</td><td class="tnum">{{ $j->attempts }}</td><td>{{ \Carbon\Carbon::createFromTimestamp($j->available_at)->format('d M Y H:i') }}</td><td>{{ \Carbon\Carbon::createFromTimestamp($j->created_at)->format('d M Y H:i') }}</td>
<td>@if(Route::has('system.queue.delete'))<form method="POST" action="{{ route('system.queue.delete', $j->id) }}?queue=jobs" class="d-inline" onsubmit="return confirm('Delete job?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endif</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($jobs ?? null, 'links'))<div class="mt-3">{{ $jobs->links('pagination::bootstrap-5', ['pageName' => 'jobs_page']) }}</div>@endif
</div></div>
<div class="card shadow-sm"><div class="card-header fw-semibold">Failed Jobs ({{ is_countable($failed ?? null) ? count($failed) : ($failed->total() ?? 0) }})</div><div class="card-body">
<x-datatable id="failedJobsTable">
<thead><tr><th>ID</th><th>Queue</th><th>Exception</th><th>Failed At</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($failed ?? []) as $f)
<tr><td class="tnum">{{ $f->id }}</td><td>{{ $f->queue ?? 'default' }}</td><td class="small text-muted">{{ \Illuminate\Support\Str::limit($f->exception ?? '', 120) }}</td><td>{{ $f->failed_at }}</td>
<td class="text-nowrap">
    @if(Route::has('system.queue.retry'))<form method="POST" action="{{ route('system.queue.retry', $f->id) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-primary">Retry</button></form>@endif
    @if(Route::has('system.queue.delete'))<form method="POST" action="{{ route('system.queue.delete', $f->id) }}?queue=failed" class="d-inline" onsubmit="return confirm('Delete failed job?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endif
</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($failed ?? null, 'links'))<div class="mt-3">{{ $failed->links('pagination::bootstrap-5', ['pageName' => 'failed_page']) }}</div>@endif
</div></div>
@endsection
