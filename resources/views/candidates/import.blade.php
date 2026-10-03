@extends('layouts.app')
@section('title','Import Candidates')
@section('content')
<h4 class="mb-3">Import Candidates (CSV)</h4>
<div class="card shadow-sm mb-3"><div class="card-body">
<p class="text-muted small mb-2">CSV columns: first_name, last_name, email, phone, dob (YYYY-MM-DD), gender, nationality, preferred_destination</p>
<form method="POST" action="{{ route('candidates.import.store') }}" enctype="multipart/form-data" onsubmit="this.querySelector('button').disabled=true">@csrf
<div class="row g-2 align-items-end"><div class="col-md-6"><label class="form-label small">CSV file</label><input type="file" name="file" accept=".csv,.txt" class="form-control" required></div>
<div class="col-md-3"><button class="btn btn-primary">Upload &amp; Preview</button></div></div>
</form>
</div></div>
@if(!empty($preview))
<div class="card shadow-sm"><div class="card-body">
<h6 class="mb-2">Preview ({{ count($preview) }} rows)</h6>
<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Note</th></tr></thead>
<tbody>@foreach($preview as $row)<tr><td>{{ $row['first_name'] ?? '' }} {{ $row['last_name'] ?? '' }}</td><td>{{ $row['email'] ?? '' }}</td><td>{{ $row['phone'] ?? '' }}</td><td>{{ $row['_note'] ?? '' }}</td></tr>@endforeach</tbody></table></div>
<form method="POST" action="{{ route('candidates.import.confirm') }}" onsubmit="this.querySelector('button').disabled=true">@csrf<button class="btn btn-success">Confirm Import</button>
<a href="{{ route('candidates.index') }}" class="btn btn-outline-secondary">Cancel</a></form>
</div></div>
@endif
@endsection
