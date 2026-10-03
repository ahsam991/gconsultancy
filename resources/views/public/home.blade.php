@extends('public.layout')
@section('title', 'Global Consultancy | Study in the UK, USA, Canada, Australia & Europe')
@section('content')

{{-- HERO: asymmetric, one intent --}}
<section class="gc-hero">
<div class="container py-5"><div class="row g-4 align-items-center">
<div class="col-lg-7">
    <span class="gc-eyebrow">Free counselling · UK · USA · Canada · Australia · Europe</span>
    <h1>Get into a British university, guided start to finish.</h1>
    <hr class="gc-hero-rule">
    <p class="lead">Course shortlisting, applications, CAS, visa and pre-departure — one counsellor owns your file from first call to arrival.</p>
    <div class="d-flex gap-2 flex-wrap mt-3">
        <a href="{{ Route::has('appointment.book') ? route('appointment.book') : url('/book-appointment') }}" class="btn btn-primary btn-lg">Book free counselling</a>
        <a href="{{ Route::has('apply') ? route('apply') : url('/apply-online') }}" class="btn btn-outline-light btn-lg">Apply online</a>
    </div>
    <div class="gc-proof">
        <div><div class="n tnum">{{ number_format($stats['offers'] ?? 0) }}+</div><div class="l">Offers secured</div></div>
        <div><div class="n tnum">{{ $stats['visa_rate'] ?? 0 }}%</div><div class="l">Visa grant rate</div></div>
        <div><div class="n tnum">{{ $stats['partners'] ?? 0 }}</div><div class="l">Partner universities</div></div>
    </div>
</div>
<div class="col-lg-5">
    <div class="gc-hero-card p-4">
        <span class="gc-eyebrow blue">Your application journey</span>
        <ol class="list-unstyled mb-0">
        @foreach(['Profile & documents','Offer received','CAS issued','Visa granted','Enrolled'] as $i => $s)
        <li class="d-flex align-items-center gap-2 py-2 {{ $loop->last ? '' : 'border-bottom' }}" style="border-color:var(--gc-line-soft)!important">
            <span class="gc-step-num" style="width:30px;height:30px;font-size:.85rem">{{ $i + 1 }}</span>
            <span class="fw-semibold">{{ $s }}</span>
            @if($loop->last)<span class="gc-stamp ok sealed ms-auto">Sealed</span>@endif
        </li>
        @endforeach
        </ol>
        <a href="{{ Route::has('course-finder') ? route('course-finder') : url('/course-finder') }}" class="btn btn-brass w-100 mt-3">Find my course</a>
    </div>
</div>
</div></div>
</section>

{{-- DESTINATIONS --}}
<section class="gc-section"><div class="container">
<span class="gc-eyebrow">Study destinations</span>
<h2>Five countries, one desk.</h2>
<p class="text-muted" style="max-width:38rem">Every destination has its own visa logic and intake calendar. Pick a lane — your counsellor maps the rest.</p>
<div class="row g-3 mt-1">
@foreach(['UK'=>'September & January intakes · CAS route','USA'=>'F-1 visa · Fall & Spring','Canada'=>'Study permit · SDS route','Australia'=>'CoE + Genuine Student','Europe'=>'Low tuition · English-taught'] as $d => $blurb)
<div class="col-6 col-lg-4"><a href="{{ url('/study/' . $d) }}" class="gc-dest-card d-block">
<span class="gc-eyebrow">{{ $d }}</span>
<div class="flag">{{ ['UK'=>'United Kingdom','USA'=>'United States','Canada'=>'Canada','Australia'=>'Australia','Europe'=>'Europe'][$d] }}</div>
<p class="small text-muted mb-0 mt-1">{{ $blurb }}</p>
</a></div>
@endforeach
<div class="col-6 col-lg-4"><a href="{{ Route::has('course-finder') ? route('course-finder') : url('/course-finder') }}" class="gc-dest-card d-block" style="border-style:dashed">
<span class="gc-eyebrow blue">Not sure?</span>
<div class="flag">Course Finder</div>
<p class="small text-muted mb-0 mt-1">Filter by subject, budget and IELTS.</p>
</a></div>
</div>
</div></section>

{{-- HOW IT WORKS --}}
<section class="gc-section pt-0"><div class="container">
<span class="gc-eyebrow">How it works</span>
<h2>Three meetings to an offer.</h2>
<div class="row g-3 mt-1">
<div class="col-md-4"><div class="d-flex gap-3"><span class="gc-step-num">1</span><div><strong>Free counselling</strong><p class="small text-muted mb-0">Grades, budget, IELTS → a shortlist that fits.</p></div></div></div>
<div class="col-md-4"><div class="d-flex gap-3"><span class="gc-step-num">2</span><div><strong>Application & documents</strong><p class="small text-muted mb-0">SOP, LORs, transcripts — checked, then submitted.</p></div></div></div>
<div class="col-md-4"><div class="d-flex gap-3"><span class="gc-step-num">3</span><div><strong>Offer → CAS → visa</strong><p class="small text-muted mb-0">Deposit, CAS interview prep, visa filing, arrival.</p></div></div></div>
</div>
</div></section>

{{-- FEATURED UNIVERSITIES --}}
@if(($universities ?? collect())->isNotEmpty())
<section class="gc-section pt-0"><div class="container">
<div class="d-flex justify-content-between align-items-end"><div><span class="gc-eyebrow">Partner universities</span><h2>Where our students land.</h2></div>
<a href="{{ url('/universities') }}" class="btn btn-sm btn-outline-primary">All universities</a></div>
<div class="row g-3 mt-1">
@foreach($universities as $u)
<div class="col-md-4"><div class="gc-dest-card"><div class="flag" style="font-size:1.1rem">{{ $u->name }}</div>
<p class="small text-muted mb-2">{{ $u->city ?? '' }}{{ isset($u->country) ? ' · ' . (is_string($u->country) ? $u->country : ($u->country->name ?? '')) : '' }}</p>
<a href="{{ url('/universities/' . $u->id) }}" class="small fw-semibold" style="color:var(--gc-accent)">View courses →</a></div></div>
@endforeach
</div>
</div></section>
@endif

{{-- FEATURED COURSES --}}
@if(($courses ?? collect())->isNotEmpty())
<section class="gc-section pt-0"><div class="container">
<div class="d-flex justify-content-between align-items-end"><div><span class="gc-eyebrow">Featured courses</span><h2>Intakes open now.</h2></div>
<a href="{{ url('/courses') }}" class="btn btn-sm btn-outline-primary">All courses</a></div>
<div class="row g-3 mt-1">
@foreach($courses as $c)
<div class="col-md-4"><div class="gc-dest-card"><div class="flag" style="font-size:1.05rem">{{ $c->name }}</div>
<p class="small text-muted mb-1">{{ $c->university->name ?? '' }}</p>
<p class="small mb-0 tnum">£{{ number_format($c->tuition_fee ?? 0) }} · IELTS {{ $c->ielts_required ?? '—' }}</p></div></div>
@endforeach
</div>
</div></section>
@endif

{{-- TESTIMONIALS --}}
@if(($testimonials ?? collect())->isNotEmpty())
<section class="gc-section pt-0"><div class="container">
<span class="gc-eyebrow">Student voices</span>
<h2>They flew. They stayed.</h2>
<div class="row g-3 mt-1">
@foreach($testimonials as $t)
<div class="col-md-4"><figure class="gc-dest-card mb-0"><blockquote class="small">“{{ \Illuminate\Support\Str::limit($t->content ?? '', 160) }}”</blockquote>
<figcaption class="small fw-semibold mt-2">{{ $t->name ?? $t->candidate_name ?? 'Student' }} <span class="text-muted fw-normal">· {{ $t->country ?? $t->university ?? '' }}</span></figcaption></figure></div>
@endforeach
</div>
</div></section>
@endif

{{-- FAQ + RISK REVERSAL --}}
@if(($faqs ?? collect())->isNotEmpty())
<section class="gc-section pt-0"><div class="container"><div class="row g-4">
<div class="col-lg-7">
<span class="gc-eyebrow">Asked every week</span>
<h2>Straight answers.</h2>
<div class="accordion gc-faq mt-3" id="homeFaq">
@foreach($faqs as $f)
<div class="accordion-item"><h3 class="accordion-header"><button class="accordion-button {{ $loop->first ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#hf{{ $f->id }}">{{ $f->question }}</button></h3>
<div id="hf{{ $f->id }}" class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}" data-bs-parent="#homeFaq"><div class="accordion-body small">{{ $f->answer }}</div></div></div>
@endforeach
</div>
</div>
<div class="col-lg-5">
<div class="gc-cta-band p-4 h-100">
<span class="gc-eyebrow" style="color:#e8c88a">No risk to start</span>
<h2 class="text-white">Counselling is free. Advice is honest.</h2>
<p class="small" style="color:#cfd6e4">No file-opening charge. No hidden fees. If no university fits your profile, we say so in the first meeting.</p>
<a href="{{ Route::has('appointment.book') ? route('appointment.book') : url('/book-appointment') }}" class="btn btn-brass btn-lg w-100">Book free counselling</a>
</div>
</div>
</div></div></section>
@endif

{{-- FINAL CTA --}}
<section class="gc-section pt-0"><div class="container">
<div class="gc-cta-band p-4 p-md-5 text-center">
<span class="gc-eyebrow" style="color:#e8c88a">September intake closes soon</span>
<h2 class="text-white">Your offer letter is one conversation away.</h2>
<div class="d-flex gap-2 justify-content-center flex-wrap mt-3">
<a href="{{ Route::has('appointment.book') ? route('appointment.book') : url('/book-appointment') }}" class="btn btn-brass btn-lg">Book free counselling</a>
<a href="{{ Route::has('apply') ? route('apply') : url('/apply-online') }}" class="btn btn-outline-light btn-lg">Apply online</a>
</div>
</div>
</div></section>
@endsection
