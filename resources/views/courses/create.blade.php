@extends('layouts.app')
@section('title','Add Course')
@section('content')
<h4 class="mb-3">Add Course</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('courses.store') }}">@csrf @include('courses._form')<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
