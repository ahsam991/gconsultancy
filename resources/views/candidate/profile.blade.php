@extends('layouts.app')
@section('title','My Profile')
@section('content')
<h4 class="mb-3">My Profile</h4>
<div class="card shadow-sm"><div class="card-body"><dl class="row mb-0">
<dt class="col-4">Name</dt><dd class="col-8">{{ auth()->user()->name ?? '' }}</dd>
<dt class="col-4">Email</dt><dd class="col-8">{{ auth()->user()->email ?? '' }}</dd>
</dl></div></div>
@endsection
