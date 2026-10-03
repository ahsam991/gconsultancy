@extends('public.layout')

@section('title', 'Contact Us | Global Consultancy')
@section('meta_description', 'Contact our counsellors by phone, email or the enquiry form.')

@section('content')
<div class="container py-5">
    <h1>Contact us</h1>
    <div class="row g-4">
        <div class="col-12 col-md-5">
            <h2 class="h5">Office info</h2>
            <address>House 12, Road 5, Dhaka, Bangladesh<br>Phone: +880-1XXX-XXXXXX<br>Email: <a href="mailto:info@globalconsultancy.com">info@globalconsultancy.com</a><br>Hours: Mon–Sat, 10am–7pm</address>
        </div>
        <div class="col-12 col-md-7">
            <h2 class="h5">Send an enquiry</h2>
            <form action="{{ Route::has('enquiry.store') ? route('enquiry.store') : url('/enquiry') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="Contact">
                <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off" aria-hidden="true">
                <div class="mb-2"><label class="form-label" for="c-name">Full name</label><input id="c-name" name="name" value="{{ old('name') }}" class="form-control" required></div>
                <div class="row g-2">
                    <div class="col-12 col-md-6"><label class="form-label" for="c-email">Email</label><input id="c-email" name="email" type="email" value="{{ old('email') }}" class="form-control" required></div>
                    <div class="col-12 col-md-6"><label class="form-label" for="c-phone">Phone</label><input id="c-phone" name="phone" value="{{ old('phone') }}" class="form-control" required></div>
                </div>
                <div class="mb-2 mt-2"><label class="form-label" for="c-msg">Message</label><textarea id="c-msg" name="message" rows="4" class="form-control" required>{{ old('message') }}</textarea></div>
                <button class="btn btn-primary btn-lg w-100" type="submit">Send enquiry</button>
            </form>
        </div>
    </div>
</div>
@endsection
