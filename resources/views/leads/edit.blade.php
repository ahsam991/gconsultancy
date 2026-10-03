@extends('layouts.app')
@section('title','Edit Lead')
@section('content')
<h4 class="mb-3">Edit Lead</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('leads.update', $lead) }}">@csrf @method('PUT')
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">First Name <span class="text-danger">*</span></label><input name="first_name" value="{{ old('first_name', $lead->first_name ?? '') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Last Name</label><input name="last_name" value="{{ old('last_name', $lead->last_name ?? '') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Status</label><select name="status" class="form-select">@foreach(['new','contacted','qualified','converted','lost'] as $s)<option @selected(old('status',$lead->status ?? '')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Source</label><input name="source" value="{{ old('source', $lead->source->name ?? '') }}" class="form-control"></div>
</div>
<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('leads.show', $lead) }}" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
@endsection
