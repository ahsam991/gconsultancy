@extends('layouts.app')
@section('title', 'Add Banner')
@section('content')
<h4 class="mb-3">Add Banner</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.banners.store') ? route('cms.banners.store') : url('/crm/cms/banners') }}" enctype="multipart/form-data">
    @csrf
    @include('cms.banners._form', ['banner' => null])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Save</button>
        <a href="{{ Route::has('cms.banners.index') ? route('cms.banners.index') : url('/crm/cms/banners') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
