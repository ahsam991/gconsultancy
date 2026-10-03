@extends('layouts.app')
@section('title','Edit Email Template')
@section('content')
<h4 class="mb-3">Edit Email Template</h4>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ route('email-templates.update', $template) }}" onsubmit="this.querySelector('button').disabled=true">@csrf @method('PUT')
@include('email_templates._form')
<button class="btn btn-primary">Save</button>
<a href="{{ route('email-templates.show', $template) }}" class="btn btn-outline-secondary">Cancel</a>
</form>
</div></div>
@endsection
