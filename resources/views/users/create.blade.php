@extends('layouts.app')
@section('title','New User')
@section('content')
<h4 class="mb-3">New User</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('users.store') }}">@csrf @include('users._form')<div class="d-flex gap-2"><button class="btn btn-primary">Create</button><a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
