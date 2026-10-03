@extends('layouts.app')
@section('title','Edit Candidate')
@section('content')
<h4 class="mb-3">Edit Candidate — {{ ($candidate->first_name ?? '') }} {{ ($candidate->last_name ?? '') }}</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('candidates.update', $candidate) }}">@csrf @method('PUT')
@include('candidates._form')
<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('candidates.show', $candidate) }}" class="btn btn-outline-secondary">Cancel</a></div>
</form>
</div></div>
@endsection
