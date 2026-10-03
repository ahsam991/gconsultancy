@extends('layouts.app')
@section('title','Edit FAQ')
@section('content')
<h4 class="mb-3">Edit FAQ</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.faqs.update', $item) }}">@csrf @method('PUT') @include('cms.faqs.form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('cms.faqs.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
