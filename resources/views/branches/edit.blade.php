@extends('layouts.app')
@section('title','Edit Branch')
@section('content')
<h4 class="mb-3">Edit Branch — {{ $branch->name }}</h4>
<div class="card shadow-sm"><div class="card-body">
@if(Route::has('branches.update'))
<form method="POST" action="{{ route('branches.update', $branch) }}">@csrf @method('PUT')
@include('branches._form')
<button class="btn btn-primary">Save Changes</button>
@if(Route::has('branches.show'))<a href="{{ route('branches.show', $branch) }}" class="btn btn-outline-secondary">Cancel</a>@endif
</form>
@endif
</div></div>
@endsection
