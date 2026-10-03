@extends('layouts.app')
@section('title', 'Add Form')
@section('content')
<h4 class="mb-3">Add Form</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.forms.store') ? route('cms.forms.store') : url('/crm/cms/forms') }}">
    @csrf
    @include('cms.forms._form', ['form' => null])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ Route::has('cms.forms.index') ? route('cms.forms.index') : url('/crm/cms/forms') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
