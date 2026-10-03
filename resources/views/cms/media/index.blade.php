@extends('layouts.app')
@section('title', 'Media Library')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Media Library</h4>
    <form method="GET" action="{{ Route::has('cms.media.index') ? route('cms.media.index') : url('/crm/cms/media') }}" class="d-flex gap-2">
        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search name or alt…" aria-label="Search media">
        <button class="btn btn-sm btn-outline-primary">Search</button>
    </form>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>Upload File (max 2MB)</h6>
    <form method="POST" action="{{ Route::has('cms.media.store') ? route('cms.media.store') : url('/crm/cms/media') }}" enctype="multipart/form-data" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-4">
            <label class="form-label small" for="file">File</label>
            <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label small" for="name">Name (optional)</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" maxlength="255">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3">
            <label class="form-label small" for="alt">Alt Text</label>
            <input type="text" name="alt" id="alt" value="{{ old('alt') }}" class="form-control @error('alt') is-invalid @enderror" maxlength="255">
            @error('alt')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2"><button class="btn btn-primary btn-sm">Upload</button></div>
    </form>
</div></div>

<div class="row">
@forelse(($media ?? []) as $m)
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm h-100">
            @if($m->mime && str_starts_with($m->mime, 'image/'))
                <img src="{{ asset('storage/' . $m->path) }}" alt="{{ $m->alt ?? $m->name }}" class="card-img-top" style="height:140px;object-fit:cover">
            @endif
            <div class="card-body p-2">
                <p class="small fw-semibold mb-0 text-truncate">{{ $m->name }}</p>
                <p class="small text-muted mb-1">{{ $m->mime ?? '' }} · {{ $m->size_kb ?? 0 }} KB</p>
                <form method="POST" action="{{ Route::has('cms.media.update') ? route('cms.media.update', $m) : url('/crm/cms/media/' . $m->id) }}">
                    @csrf @method('PUT')
                    <div class="mb-1">
                        <label class="form-label small mb-0" for="rename_{{ $m->id }}">Rename</label>
                        <input type="text" name="name" id="rename_{{ $m->id }}" value="{{ old('name', $m->name) }}" class="form-control form-control-sm @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-1">
                        <label class="form-label small mb-0" for="alt_{{ $m->id }}">Alt text</label>
                        <input type="text" name="alt" id="alt_{{ $m->id }}" value="{{ old('alt', $m->alt) }}" class="form-control form-control-sm">
                    </div>
                    <div class="d-flex gap-1 mt-1">
                        <button class="btn btn-sm btn-outline-primary">Rename</button>
                    </div>
                </form>
                <form method="POST" action="{{ Route::has('cms.media.destroy') ? route('cms.media.destroy', $m) : url('/crm/cms/media/' . $m->id) }}" class="mt-1" onsubmit="return confirm('Delete?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="col-12"><div class="alert alert-info">No media found. Upload your first file above.</div></div>
@endforelse
</div>
@if(isset($media) && method_exists($media, 'links')){{ $media->withQueryString()->links() }}@endif
@endsection
