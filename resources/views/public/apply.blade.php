@extends('public.layout')

@section('title', 'Apply Online | Global Consultancy')
@section('meta_description', 'Start your application online: personal details, academic background and study preferences.')

@section('content')
<div class="container py-5">
    <h1>Apply online</h1>
    <p class="lead">Fill this in — a counsellor will call you within 24 hours.</p>
    <form action="{{ Route::has('enquiry.store') ? route('enquiry.store') : url('/enquiry') }}" method="POST" class="col-12 col-lg-8">
        @csrf
        <input type="hidden" name="type" value="Apply">
        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">
        <h2 class="h5 mt-3">Personal details</h2>
        <div class="row g-2">
            <div class="col-12 col-md-6"><label class="form-label" for="ap-name">Full name</label><input id="ap-name" name="name" value="{{ old('name') }}" class="form-control" required></div>
            <div class="col-6 col-md-3"><label class="form-label" for="ap-dob">Date of birth</label><input id="ap-dob" name="dob" type="date" value="{{ old('dob') }}" class="form-control"></div>
            <div class="col-6 col-md-3"><label class="form-label" for="ap-phone">Phone</label><input id="ap-phone" name="phone" value="{{ old('phone') }}" class="form-control" required></div>
            <div class="col-12"><label class="form-label" for="ap-email">Email</label><input id="ap-email" name="email" type="email" value="{{ old('email') }}" class="form-control" required></div>
        </div>
        <h2 class="h5 mt-3">Academic background</h2>
        <div class="row g-2">
            <div class="col-12 col-md-6"><label class="form-label" for="ap-qual">Highest qualification</label><input id="ap-qual" name="qualification" value="{{ old('qualification') }}" class="form-control" placeholder="e.g. HSC / Bachelor"></div>
            <div class="col-6 col-md-3"><label class="form-label" for="ap-year">Passing year</label><input id="ap-year" name="passing_year" value="{{ old('passing_year') }}" class="form-control" inputmode="numeric"></div>
            <div class="col-6 col-md-3"><label class="form-label" for="ap-ielts">IELTS / PTE score</label><input id="ap-ielts" name="english_score" value="{{ old('english_score') }}" class="form-control" placeholder="e.g. 6.5 / booked"></div>
        </div>
        <h2 class="h5 mt-3">Study preference</h2>
        <div class="row g-2">
            <div class="col-6"><label class="form-label" for="ap-dest">Destination</label><select id="ap-dest" name="destination" class="form-select"><option>UK</option><option>USA</option><option>Canada</option><option>Australia</option><option>Europe</option></select></div>
            <div class="col-6"><label class="form-label" for="ap-intake">Intake</label><select id="ap-intake" name="intake" class="form-select"><option>September</option><option>January</option><option>May</option></select></div>
            <div class="col-12"><label class="form-label" for="ap-subject">Subject of interest</label><input id="ap-subject" name="subject" value="{{ old('subject') }}" class="form-control" placeholder="e.g. Computer Science"></div>
        </div>
        <button class="btn btn-primary btn-lg w-100 mt-3" type="submit">Submit application</button>
    </form>
</div>
@endsection
