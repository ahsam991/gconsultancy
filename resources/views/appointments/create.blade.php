@extends('layouts.app')
@section('title','New Appointment')
@section('content')
<h4 class="mb-3">New Appointment</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('appointments.store') }}">@csrf @include('appointments._form')<div class="d-flex gap-2"><button class="btn btn-primary">Create</button><a href="{{ route('appointments.index') }}" class="btn btn-outline-secondary">Cancel</a></div></form></div></div>
@endsection
