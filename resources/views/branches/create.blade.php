@extends('layouts.app')
@section('title','New Branch')
@section('content')
<h4 class="mb-3">New Branch</h4>
<div class="card shadow-sm"><div class="card-body">
@if(Route::has('branches.store'))
<form method="POST" action="{{ route('branches.store') }}">@csrf
@include('branches._form')
<button class="btn btn-primary">Create Branch</button>
@if(Route::has('branches.index'))<a href="{{ route('branches.index') }}" class="btn btn-outline-secondary">Cancel</a>@endif
</form>
@endif
</div></div>
@endsection
