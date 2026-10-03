@php $d = $document ?? null; @endphp
<div class="card shadow-sm h-100"><div class="card-body">
    <div class="d-flex gap-2 align-items-start"><i class="fa-solid fa-file-lines fs-4 text-primary"></i>
    <div class="flex-grow-1"><div class="fw-semibold small">{{ $d->original_name ?? $d->name ?? 'Document' }}</div>
    <div class="small text-muted">{{ $d->type ?? '' }} · v{{ $d->version ?? 1 }}</div>
    <div class="mt-1"><x-status-badge :status="$d->status ?? 'pending'"/></div></div></div>
    <div class="d-flex gap-1 mt-2 flex-wrap">
        <a href="{{ route('documents.download', $d) }}" class="btn btn-sm btn-outline-primary">Download</a>
        <form method="POST" action="{{ route('documents.verify', $d) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Verify</button></form>
        <form method="POST" action="{{ route('documents.reject', $d) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-danger">Reject</button></form>
    </div>
</div></div>
