@extends('layouts.app')
@section('title','Edit Course')
@section('content')
<h4 class="mb-3">Edit {{ $course->name ?? '' }}</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('courses.update', $course) }}">@csrf @method('PUT') @include('courses._form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
