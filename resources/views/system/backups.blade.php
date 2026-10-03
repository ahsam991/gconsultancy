@extends('layouts.app')
@section('title','Backups')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Backups</h4>
    @if(Route::has('system.health'))<a href="{{ route('system.health') }}" class="btn btn-sm btn-outline-secondary">System Health</a>@endif
</div>
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">New Backup</div><div class="card-body">
@if(Route::has('system.backups.store'))
<form method="POST" action="{{ route('system.backups.store') }}" class="row g-2">@csrf
    <div class="col-md-3"><label class="form-label">Type <span class="text-danger">*</span></label><select name="type" class="form-select" required><option value="database">database (mysqldump if available, else SQLite copy to storage/app/backups)</option><option value="files">files (note only)</option></select></div>
    <div class="col-md-5"><label class="form-label">Notes</label><input name="notes" class="form-control" maxlength="1000"></div>
    <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary">Run Backup</button></div>
</form>
<p class="small text-muted mt-2 mb-0">Or run <code>php artisan app:backup-db --type=database</code> from the server scheduler.</p>
@endif
</div></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="backupsTable">
<thead><tr><th>ID</th><th>Type</th><th>Path</th><th>Size</th><th>Status</th><th>By</th><th>Created</th><th>Actions</th></tr></thead>
<tbody>
@forelse(($backups ?? []) as $b)
<tr><td class="tnum">{{ $b->id }}</td><td>{{ $b->type }}</td><td><code class="small">{{ $b->path }}</code></td><td class="tnum">{{ $b->size_kb }} KB</td><td><x-status-badge :status="$b->status"/></td><td>{{ $b->creator->name ?? 'console' }}</td><td>{{ $b->created_at?->format('d M Y H:i') }}</td>
<td>@if(Route::has('system.backups.download'))<a href="{{ route('system.backups.download', $b) }}" class="btn btn-sm btn-outline-primary">Download (admin only)</a>@endif</td></tr>
@empty
@endforelse
</tbody>
</x-datatable>
@if(method_exists($backups ?? null, 'links'))<div class="mt-3">{{ $backups->links() }}</div>@endif
</div></div>
@endsection
