@extends('layouts.app')
@section('title','Email Template')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
<h4 class="mb-0">{{ $template->name }} <x-status-badge :status="$template->active ? 'active' : 'inactive'"/></h4>
<div class="d-flex gap-2"><a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-warning">Edit</a><a href="{{ route('email-templates.index') }}" class="btn btn-sm btn-outline-secondary">Back</a></div>
</div>
<div class="card shadow-sm"><div class="card-body">
<dl class="row"><dt class="col-3">Slug</dt><dd class="col-9"><code>{{ $template->slug }}</code></dd>
<dt class="col-3">Subject</dt><dd class="col-9">{{ $template->subject }}</dd>
<dt class="col-3">Variables</dt><dd class="col-9">{{ $template->variables ?? '—' }}</dd></dl>
<h6>Body preview</h6>
<div class="border rounded p-3 bg-light" style="white-space:pre-wrap">{{ $template->body }}</div>
</div></div>
@endsection
