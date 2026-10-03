@extends('layouts.app')
@section('title','Staff applications')
@section('content')
<h4 class="mb-3 text-capitalize">applications</h4>
<div class="card shadow-sm"><div class="card-body"><p class="text-muted">See full CRM module: <a href="{{ Route::has('applications.index') ? route('applications.index') : '#' }}">applications index</a>.</p></div></div>
@endsection
