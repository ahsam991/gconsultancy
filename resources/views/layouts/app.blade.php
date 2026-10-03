<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Global Consultancy CRM')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Instrument+Serif:ital@0;1&family=Newsreader:opsz,wght@6..72,500;6..72,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/gc-design.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/solid.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.0/dist/cdn.min.js"></script>
    <style>
        body{font-family:'Inter',sans-serif;background:#f8fafc;color:#1e293b}
        a{text-decoration:none;color:inherit}
        .sidebar{width:250px;min-height:100vh;position:fixed;top:0;left:0;z-index:100;background:#fff;border-right:1px solid #e2e8f0;display:flex;flex-direction:column}
        .main-wrap{margin-left:250px;min-height:100vh;display:flex;flex-direction:column}
        .sidebar .nav-link{color:#475569;font-size:.9rem;padding:.5rem 1rem;border-radius:.375rem}
        .sidebar .nav-link:hover,.sidebar .nav-link.active{background:#eff6ff;color:#0d6efd}
        .sidebar .nav-small{list-style:none;padding-left:2.2rem}
        @media(max-width:992px){.sidebar{display:none}.main-wrap{margin-left:0}}
    </style>
    @stack('styles')
</head>
<body>
<div class="d-flex">
    @include('layouts.components.sidebar')
    <div class="main-wrap flex-grow-1">
        <nav class="navbar navbar-expand-lg gc-topbar sticky-top">
            <div class="container-fluid">
                <a class="navbar-brand fw-bold" href="{{ Route::has('dashboard') ? route('dashboard') : url('/') }}">
                    <i class="fa-solid fa-building-flag me-2 text-primary"></i>Global Consultancy
                </a>
                @auth
                @if(Route::has('search'))
                <form method="GET" action="{{ route('search') }}" class="d-none d-md-flex me-2" role="search">
                    <div class="input-group input-group-sm"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Search…" aria-label="Global search" minlength="2"><button class="btn btn-outline-secondary" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button></div>
                </form>
                @endif
                @endauth
                <ul class="navbar-nav ms-auto align-items-center flex-row gap-2">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ Route::has('notifications.index') ? route('notifications.index') : '#' }}" title="Notifications">
                            <i class="fa-solid fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $unreadNotifications ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle fw-medium" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-user me-1"></i>{{ auth()->user()->name ?? 'User' }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text small text-muted">{{ auth()->user()->email ?? '' }}</span></li>
                            <li><hr class="dropdown-divider"></li>
                            @if(Route::has('2fa.setup'))<li><a class="dropdown-item" href="{{ route('2fa.setup') }}"><i class="fa-solid fa-shield-halved me-2"></i>Two-Factor Auth</a></li>@endif
                            <li>
                                <form method="POST" action="{{ Route::has('logout') ? route('logout') : url('/logout') }}" onsubmit="this.querySelector('button').disabled=true">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                    @else
                    <li class="nav-item"><a class="btn btn-sm btn-primary" href="{{ route('login') }}">Sign In</a></li>
                    @endauth
                </ul>
            </div>
        </nav>
        <main class="p-3 p-md-4 flex-grow-1">
            <div class="container-fluid">
                @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
                @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
                @if($errors->any() && !isset($hideTopErrors))<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
                @yield('content')
            </div>
        </main>
        <footer class="bg-white border-top py-3"><div class="container-fluid d-flex justify-content-between small text-muted"><span>Global Consultancy Education CRM</span><span>v1.0.0</span></div></footer>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
(function(){var r=window.matchMedia('(prefers-reduced-motion: reduce)').matches;
function show(el){el.classList.add('in')}
var els=document.querySelectorAll('.anim,.reveal');
if('IntersectionObserver' in window&&!r){var io=new IntersectionObserver(function(es){es.forEach(function(e){if(e.isIntersecting){show(e.target);io.unobserve(e.target)}})},{threshold:.12});els.forEach(function(el){io.observe(el)})}
else{els.forEach(show)}})();
</script>
@auth
@if(auth()->user()->role?->name === 'candidate')
<nav class="d-md-none fixed-bottom bg-white border-top" aria-label="Portal navigation"><div class="d-flex justify-content-around py-2 small">
<a href="{{ route('portal.dashboard') }}" class="text-center"><i class="fa-solid fa-house d-block"></i>Home</a>
<a href="{{ route('portal.applications') }}" class="text-center"><i class="fa-solid fa-file-lines d-block"></i>Apps</a>
<a href="{{ route('portal.documents') }}" class="text-center"><i class="fa-solid fa-folder-open d-block"></i>Docs</a>
<a href="{{ route('portal.appointments') }}" class="text-center"><i class="fa-solid fa-calendar-days d-block"></i>Visits</a>
<a href="{{ route('portal.profile') }}" class="text-center"><i class="fa-solid fa-user d-block"></i>Profile</a>
</div></nav>
<style>@media(max-width:767px){main{padding-bottom:70px}}</style>
@endif
@endauth
<script>
document.addEventListener('DOMContentLoaded',function(){
    if(window.jQuery && jQuery.fn.DataTable){jQuery('.data-table').each(function(){if(!jQuery.fn.DataTable.isDataTable(this)){jQuery(this).DataTable({responsive:true,pageLength:25,ordering:true,autoWidth:false});}});}
    document.querySelectorAll('form[method="POST"]').forEach(function(f){f.addEventListener('submit',function(){var b=f.querySelector('button[type=submit]');if(b&&!f.dataset.nodbl){b.disabled=true;}});});
    var tl=document.querySelectorAll('.toast');tl.forEach(function(e){if(window.bootstrap){new bootstrap.Toast(e).show();}});
});
</script>
@stack('scripts')
</body>
</html>
