@extends('layouts.app')
@section('title','Document')
@section('content')
<h4 class="mb-3">Document Detail</h4>
<div class="row"><div class="col-md-6">@include('documents._card',['document'=>$document])</div>
<div class="col-md-6"><div class="card shadow-sm"><div class="card-body"><dl class="row mb-0"><dt class="col-4">Candidate</dt><dd class="col-8"><a href="{{ route('candidates.show', $document->candidate_id) }}">{{ $document->candidate->first_name ?? '' }} {{ $document->candidate->last_name ?? '' }}</a></dd><dt class="col-4">Notes</dt><dd class="col-8">{{ $document->notes ?? '—' }}</dd><dt class="col-4">Uploaded</dt><dd class="col-8">{{ $document->created_at ?? '—' }}</dd></dl><a href="{{ route('documents.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Back</a></div></div>
<div class="card shadow-sm mt-3"><div class="card-header fw-semibold">Version History (never overwritten)</div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>v</th><th>File</th><th>By</th><th>At</th></tr></thead>
<tbody>@forelse(($document->versions ?? []) as $v)<tr><td class="tnum">v{{ $v->version }}</td><td>{{ $v->original_filename }}</td><td>{{ $v->uploader->name ?? '—' }}</td><td>{{ $v->created_at ?? '' }}</td></tr>@empty<tr><td colspan="4" class="text-muted p-3">Single version.</td></tr>@endforelse</tbody></table></div></div></div></div></div>
@endsection
