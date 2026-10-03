@extends('layouts.app')
@section('title','Edit Team Member')
@section('content')
<h4 class="mb-3">Edit Team Member</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.team.update', $item) }}">@csrf @method('PUT') @include('cms.team.form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('cms.team.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
