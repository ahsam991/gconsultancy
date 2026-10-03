@extends('layouts.app')
@section('title','New Email Template')
@section('content')
<h4 class="mb-3">New Email Template</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('email-templates.store') }}" onsubmit="this.querySelector('button').disabled=true">@csrf
@include('email_templates._form')
<button class="btn btn-primary">Create</button>
<a href="{{ route('email-templates.index') }}" class="btn btn-outline-secondary">Cancel</a>
</form>
</div></div>
@endsection
