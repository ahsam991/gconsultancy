@extends('public.layout')

@section('title', 'Universities | Global Consultancy')
@section('meta_description', 'Browse partner universities in the UK, USA, Canada, Australia and Europe.')

@section('content')
<div class="container py-5">
    <h1>Universities</h1>
    <p class="lead">Partner universities across 5 destinations.</p>
    <div class="row g-3">
        @forelse(($universities ?? []) as $uni)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100"><div class="card-body">
                    <h2 class="h5">{{ $uni->name ?? $uni['name'] ?? '' }}</h2>
                    <p class="small text-muted">{{ $uni->country ?? $uni['country'] ?? '' }}</p>
                    <a class="btn btn-sm btn-outline-primary" href="{{ url('/universities/' . ($uni->id ?? $uni['id'] ?? '')) }}">View details</a>
                </div></div>
            </div>
        @empty
            <div class="col-12"><p class="text-muted">No universities found. <a href="{{ url('/contact') }}">Contact us</a> for the latest list.</p></div>
        @endforelse
    </div>
</div>
@endsection
