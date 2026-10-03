@extends('public.layout')

@section('title', 'Book an Appointment | Global Consultancy')
@section('meta_description', 'Book a free counselling appointment online or in person.')

@section('content')
<div class="container py-5">
    <h1>Book an appointment</h1>
    <p class="lead">Free 30-minute counselling — online or in person.</p>
    <form action="{{ Route::has('enquiry.store') ? route('enquiry.store') : url('/enquiry') }}" method="POST" class="col-12 col-md-8 col-lg-6">
        @csrf
        <input type="hidden" name="type" value="Appointment">
        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">
        <div class="mb-2"><label class="form-label" for="a-name">Full name</label><input id="a-name" name="name" value="{{ old('name') }}" class="form-control" required></div>
        <div class="row g-2">
            <div class="col-12 col-md-6"><label class="form-label" for="a-email">Email</label><input id="a-email" name="email" type="email" value="{{ old('email') }}" class="form-control" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="a-phone">Phone</label><input id="a-phone" name="phone" value="{{ old('phone') }}" class="form-control" required></div>
        </div>
        <div class="row g-2 mt-1">
            <div class="col-6"><label class="form-label" for="a-date">Preferred date</label><input id="a-date" name="date" type="date" value="{{ old('date') }}" class="form-control" required></div>
            <div class="col-6"><label class="form-label" for="a-time">Preferred time</label><input id="a-time" name="time" type="time" value="{{ old('time') }}" class="form-control" required></div>
        </div>
        <div class="mb-2 mt-2"><label class="form-label" for="a-mode">Mode</label><select id="a-mode" name="mode" class="form-select"><option>In person</option><option>Online (video call)</option><option>Phone call</option></select></div>
        <button class="btn btn-primary btn-lg w-100" type="submit">Request appointment</button>
    </form>
</div>
@endsection
