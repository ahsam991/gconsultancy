@extends('layouts.app')
@section('title','Add FAQ')
@section('content')
<h4 class="mb-3">Add FAQ</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.faqs.store') }}">@csrf @include('cms.faqs.form',['item'=>null])<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('cms.faqs.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
