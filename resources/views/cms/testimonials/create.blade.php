@extends('layouts.app')
@section('title','Add Testimonial')
@section('content')
<h4 class="mb-3">Add Testimonial</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('cms.testimonials.store') }}" enctype="multipart/form-data">@csrf @include('cms.testimonials.form',['item'=>null])<div class="d-flex gap-2"><button class="btn btn-primary">Save</button><a href="{{ route('cms.testimonials.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
