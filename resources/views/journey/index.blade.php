@extends('layouts.app')
@section('title','Student Journey')
@section('content')
<h4 class="mb-3">Student Journey</h4>
<div class="row g-3">
@foreach([['Offers','journey.offers','fa-solid fa-award','primary'],['Deposits','journey.deposits','fa-solid fa-coins','warning'],['CAS Records','journey.cas','fa-solid fa-passport','info'],['Visa Cases','journey.visas','fa-solid fa-plane','success'],['Enrolments','journey.enrolments','fa-solid fa-graduation-cap','secondary']] as [$label,$route,$icon,$color])
<div class="col-md-4"><div class="card shadow-sm h-100"><div class="card-body d-flex justify-content-between align-items-center">
<div><i class="{{ $icon }} text-{{ $color }} me-2"></i><span class="fw-semibold">{{ $label }}</span></div>
<a href="{{ route($route) }}" class="btn btn-sm btn-outline-{{ $color }}">Open</a>
</div></div></div>
@endforeach
</div>
@endsection
