@extends('layouts.app')
@section('title','New Team')
@section('content')
<h4 class="mb-3">New Team</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('teams.store') }}" onsubmit="this.querySelector('button').disabled=true">@csrf
@include('teams._form')
<button class="btn btn-primary">Create Team</button>
<a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
</div></div>
@endsection
