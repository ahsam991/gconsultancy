@extends('layouts.app')
@section('title','Edit Document')
@section('content')
<h4 class="mb-3">Edit Document</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('documents.update', $document) }}">@csrf @method('PUT')
<div class="row"><div class="col-md-6 mb-3"><label class="form-label">Type</label><select name="type" class="form-select"><option>{{ $document->type }}</option></select></div><div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes', $document->notes ?? '') }}</textarea></div></div>
<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
@endsection
