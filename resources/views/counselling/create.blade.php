@extends('layouts.app')
@section('title','New Counselling Session')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">New Counselling Session</h4>
    @if(Route::has('counselling.index'))
    <a href="{{ route('counselling.index') }}" class="btn btn-sm btn-outline-secondary">Back to list</a>
    @endif
</div>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('counselling.store') ? route('counselling.store') : url('/counselling') }}">
    @csrf
    @include('counselling._form')
    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Save Session</button>
        @if(Route::has('counselling.index'))
        <a href="{{ route('counselling.index') }}" class="btn btn-outline-secondary">Cancel</a>
        @endif
    </div>
</form>
</div></div>
@endsection
