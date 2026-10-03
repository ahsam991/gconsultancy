@extends('layouts.app')
@section('title', 'Edit Form')
@section('content')
<h4 class="mb-3">Edit Form</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.forms.update') ? route('cms.forms.update', $form) : url('/crm/cms/forms/' . $form->id) }}">
    @csrf @method('PUT')
    @include('cms.forms._form', ['form' => $form])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ Route::has('cms.forms.index') ? route('cms.forms.index') : url('/crm/cms/forms') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
