@extends('layouts.app')
@section('title', 'Edit Banner')
@section('content')
<h4 class="mb-3">Edit Banner</h4>
<div class="card shadow-sm"><div class="card-body">
<form method="POST" action="{{ Route::has('cms.banners.update') ? route('cms.banners.update', $banner) : url('/crm/cms/banners/' . $banner->id) }}" enctype="multipart/form-data">
    @csrf @method('PUT')
    @include('cms.banners._form', ['banner' => $banner])
    <div class="d-flex gap-2">
        <button class="btn btn-primary">Update</button>
        <a href="{{ Route::has('cms.banners.index') ? route('cms.banners.index') : url('/crm/cms/banners') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</form>
</div></div>
@endsection
