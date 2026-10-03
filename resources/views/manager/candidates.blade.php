@extends('layouts.app')
@section('title','Manager candidates')
@section('content')
<h4 class="mb-3 text-capitalize">candidates</h4>
<div class="card shadow-sm"><div class="card-body"><p class="text-muted">See full CRM module: <a href="{{ Route::has('candidates.index') ? route('candidates.index') : '#' }}">candidates index</a>.</p></div></div>
@endsection
