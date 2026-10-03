@extends('layouts.app')
@section('title','Add Team Member')
@section('content')
<h4 class="mb-3">Add Team Member</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.team.store') }}" enctype="multipart/form-data">@csrf @include('cms.team.form',['item'=>null])<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('cms.team.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
