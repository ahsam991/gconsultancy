@extends('layouts.app')
@section('title','Edit Team')
@section('content')
<h4 class="mb-3">Edit Team</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('teams.update', $team) }}" onsubmit="this.querySelector('button').disabled=true">@csrf @method('PUT')
@include('teams._form')
<button class="btn btn-primary">Save</button>
<a href="{{ route('teams.show', $team) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
</div></div>
@endsection
