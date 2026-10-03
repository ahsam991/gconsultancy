@extends('layouts.app')
@section('title','Staff tasks')
@section('content')
<h4 class="mb-3 text-capitalize">tasks</h4>
<div class="card shadow-sm"><div class="card-body"><p class="text-muted">See full CRM module: <a href="{{ Route::has('tasks.index') ? route('tasks.index') : '#' }}">tasks index</a>.</p></div></div>
@endsection
