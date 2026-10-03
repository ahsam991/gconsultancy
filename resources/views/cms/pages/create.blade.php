@extends('layouts.app')
@section('title','Add Page')
@section('content')
<h4 class="mb-3">Add Page</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.pages.store') }}" enctype="multipart/form-data">@csrf @include('cms.pages.form',['item'=>null])<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('cms.pages.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
