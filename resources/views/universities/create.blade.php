@extends('layouts.app')
@section('title','Add University')
@section('content')
<h4 class="mb-3">Add University</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('universities.store') }}">@csrf @include('universities._form')<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('universities.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
