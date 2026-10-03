@extends('layouts.app')
@section('title','New Application')
@section('content')
<h4 class="mb-3">New Application</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('applications.store') }}">@csrf @include('applications._form')<div class="d-flex gap-2"><button class="btn btn-primary">Create</button><a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
