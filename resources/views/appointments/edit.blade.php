@extends('layouts.app')
@section('title','Edit Appointment')
@section('content')
<h4 class="mb-3">Edit Appointment</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('appointments.update', $appointment) }}">@csrf @method('PUT') @include('appointments._form')<div class="d-flex gap-2"><button class="btn btn-primary">Update</button><a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
