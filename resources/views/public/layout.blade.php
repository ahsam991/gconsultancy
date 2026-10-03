<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Global Consultancy | Study Abroad Experts')</title>
    <meta name="description" content="@yield('meta_description', 'Global Consultancy helps students study in the UK, USA, Canada, Australia and Europe — counselling, admissions, visas, accommodation and pre-departure support.')">
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Global Consultancy">
    <meta property="og:title" content="@yield('title', 'Global Consultancy | Study Abroad Experts')">
    <meta property="og:description" content="@yield('meta_description', 'Study abroad counselling, admissions, visas and pre-departure support.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('images/og-cover.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ url('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,500;6..72,600;6..72,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/gc-design.css') }}">
    @stack('head')
</head>
<body>
<a class="visually-hidden-focusable" href="#main">Skip to content</a>

<header>
    <div class="gc-topstrip small">
        <div class="container d-flex justify-content-between py-1">
            <span>Hotline: {{ setting('phone_london_local', '07402 993321') }} (London) · {{ setting('phone_bd1', '01744742686') }}, {{ setting('phone_bd2', '01935017746') }} (Bangladesh) &nbsp;|&nbsp; {{ setting('email_primary', 'info@gconsultancy.co.uk') }}</span>
            <span class="d-none d-md-inline">{{ setting('business_hours', 'Always open') }} · <a href="{{ setting('social_facebook', 'https://www.facebook.com/GCEduLimited') }}" class="text-white">19K followers</a></span>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg gc-navbar sticky-top" aria-label="Main navigation">
        <div class="container">
            <a class="gc-brandmark" href="{{ url('/') }}"><span class="gc-crest"><i class="fa-solid fa-building-flag" style="font-size:.9rem"></i></span>Global Consultancy</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav" aria-controls="publicNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="publicNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/about') }}">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ url('/services') }}">Services</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Study Destinations</a>
                        <ul class="dropdown-menu">
                            @foreach(['uk' => 'UK', 'usa' => 'USA', 'canada' => 'Canada', 'australia' => 'Australia', 'europe' => 'Europe'] as $slug => $label)
                                <li><a class="dropdown-item" href="{{ Route::has('study.destination') ? route('study.destination', $slug) : url('/study/' . $slug) }}">{{ $label }}</a></li>
                            @endforeach
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="{{ Route::has('universities.index') ? route('universities.index') : url('/universities') }}">Universities</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ Route::has('courses.index') ? route('courses.index') : url('/courses') }}">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ Route::has('course.finder') ? route('course.finder') : url('/course-finder') }}">Course Finder</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ Route::has('contact') ? route('contact') : url('/contact') }}">Contact</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary" href="{{ Route::has('appointment.book') ? route('appointment.book') : url('/appointment') }}">Book Appointment</a>
                    <a class="btn btn-primary" href="{{ Route::has('apply.online') ? route('apply.online') : url('/apply') }}">Apply Online</a>
                </div>
            </div>
        </div>
    </nav>
</header>

<main id="main">
    @if(session('status'))
        <div class="container mt-3"><div class="alert alert-success" role="alert">{{ session('status') }}</div></div>
    @endif
    @if($errors->any())
        <div class="container mt-3"><div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div></div>
    @endif
    @yield('content')
</main>

<footer class="gc-footer mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-12 col-md-3">
                <h2 class="h5">{{ setting('company_name', 'Global Consultancy Education') }}</h2>
                <p class="small">{{ setting('company_usp', '100% FREE Education Counselling and Application Processing') }}</p>
                <p class="small">{{ setting('company_bio_short', '') }}</p>
            </div>
            <div class="col-12 col-md-3">
                <h2 class="h5">London Office</h2>
                <address class="small mb-0">
                    {{ setting('address_london', 'Suite 3, 2nd Floor, LMC Business Wing 38-44 Whitechapel Road, London, E1 1JX') }}<br>
                    Phone: {{ setting('phone_london_local', '07402 993321') }}<br>
                    Email: <a class="link-light" href="mailto:{{ setting('email_primary', 'info@gconsultancy.co.uk') }}">{{ setting('email_primary', 'info@gconsultancy.co.uk') }}</a>
                </address>
            </div>
            <div class="col-12 col-md-3">
                <h2 class="h5">Khulna Office</h2>
                <address class="small mb-0">
                    {{ setting('address_khulna', 'House No 52, Nirala R/A, Khulna') }}<br>
                    Phone: {{ setting('phone_bd1', '01744742686') }}, {{ setting('phone_bd2', '01935017746') }}<br>
                    <span class="text-white-50">Areas: Khulna, Bagerhat, Satkhira, Sonadanga, Khalishpur</span>
                </address>
            </div>
            <div class="col-12 col-md-3">
                <h2 class="h5">Follow us</h2>
                <p class="small">
                    <a class="link-light me-3" href="{{ setting('social_facebook', 'https://www.facebook.com/GCEduLimited') }}" rel="noopener">Facebook (19K)</a>
                    <a class="link-light me-3" href="{{ setting('social_instagram', '#') }}" rel="noopener">Instagram</a>
                    <a class="link-light me-3" href="{{ setting('social_twitter', '#') }}" rel="noopener">X</a>
                    <a class="link-light" href="{{ setting('social_youtube', '#') }}" rel="noopener">YouTube</a>
                </p>
                <p class="small"><a class="link-light" href="{{ url('/contact') }}">Contact</a> · <a class="link-light" href="{{ url('/apply-online') }}">Apply Online</a> · <a class="link-light" href="{{ url('/blog') }}">Visa Success Stories</a></p>
            </div>
        </div>
    </div>
    <div class="border-top border-secondary">
        <div class="container py-3 d-flex justify-content-between small">
            <span>&copy; {{ date('Y') }} Global Consultancy. All rights reserved.</span>
            <span>Study Abroad Experts</span>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
