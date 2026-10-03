@extends('layouts.app')
@section('title','Edit Task')
@section('content')
<h4 class="mb-3">Edit Task</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('tasks.update', $task) }}">@csrf @method('PUT') @include('tasks._form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
