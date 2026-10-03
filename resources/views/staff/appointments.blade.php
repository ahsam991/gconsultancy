@extends('layouts.app')
@section('title','Staff appointments')
@section('content')
<h4 class="mb-3 text-capitalize">appointments</h4>
<div class="card shadow-sm"><div class="card-body"><p class="text-muted">See full CRM module: <a href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}">appointments index</a>.</p></div></div>
@endsection
