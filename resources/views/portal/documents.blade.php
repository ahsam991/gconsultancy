@extends('layouts.app')
@section('title', 'My Documents')
@section('breadcrumb', 'Portal Documents')
@section('content')
<div class="container-fluid">
    <h1 class="h4">My documents</h1>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <div class="card card-body mb-3">
        <h2 class="h6">Upload a document</h2>
        <form action="{{ route('portal.documents.store') }}" method="POST" enctype="multipart/form-data" onsubmit="this.querySelector('button').disabled=true">
            @csrf
            <div class="mb-2"><label class="form-label" for="d-type">Document type</label><select id="d-type" name="document_type_id" class="form-select form-select-lg" required><option value="">Select…</option>@foreach(($types ?? []) as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach</select></div>
            <div class="mb-2"><label class="form-label" for="d-file">File (PDF/JPG/PNG/DOC, max 10MB)</label><input id="d-file" name="file" type="file" class="form-control form-control-lg" required></div>
            <button class="btn btn-primary btn-lg w-100" type="submit">Upload</button>
        </form>
    </div>
    <h2 class="h6">My uploads</h2>
    <div class="table-responsive"><table class="table table-bordered">
        <thead><tr><th>Document</th><th>Type</th><th>Status</th><th>Uploaded</th></tr></thead>
        <tbody>
            @forelse(($documents ?? []) as $d)
                <tr><td>{{ $d->original_filename }}</td><td>{{ $d->documentType->name ?? '—' }}</td><td><x-status-badge :status="$d->verification_status ?? ''"/></td><td>{{ $d->created_at?->format('d M Y') ?? '' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-muted">Nothing uploaded yet.</td></tr>
            @endforelse
        </tbody>
    </table></div>
    @if(method_exists($documents ?? null, 'links'))<div class="mt-2">{{ $documents->links() }}</div>@endif
</div>
@endsection
