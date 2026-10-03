@extends('layouts.app')
@section('title','Edit Page')
@section('content')
<h4 class="mb-3">Edit Page</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.pages.update', $item) }}">@csrf @method('PUT') @include('cms.pages.form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('cms.pages.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
