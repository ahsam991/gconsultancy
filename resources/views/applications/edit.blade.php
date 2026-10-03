@extends('layouts.app')
@section('title','Edit Application')
@section('content')
<h4 class="mb-3">Edit Application {{ $application->uid ?? '' }}</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('applications.update', $application) }}">@csrf @method('PUT') @include('applications._form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('applications.show', $application) }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
