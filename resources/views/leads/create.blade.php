@extends('layouts.app')
@section('title','Add Lead')
@section('content')
<h4 class="mb-3">Add Lead</h4>
<div class="card shadow-sm"><div class="card-body"><form method="POST" action="{{ route('leads.store') }}">@csrf
<div class="row">
<div class="col-md-6 mb-3"><label class="form-label">First Name <span class="text-danger">*</span></label><input name="first_name" value="{{ old('first_name') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Last Name</label><input name="last_name" value="{{ old('last_name') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ old('email') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label">Phone <span class="text-danger">*</span></label><input name="phone" value="{{ old('phone') }}" class="form-control" required></div>
<div class="col-md-6 mb-3"><label class="form-label">Source</label><select name="source" class="form-select">@foreach(['Walk-in','Referral','Website','Social Media','Agent','Event'] as $s)<option @selected(old('source')==$s)>{{ $s }}</option>@endforeach</select></div>
<div class="col-md-6 mb-3"><label class="form-label">Destination</label><select name="destination" class="form-select">@foreach(['UK','Canada','Australia','USA','New Zealand','Ireland'] as $d)<option @selected(old('destination')==$d)>{{ $d }}</option>@endforeach</select></div>
<div class="col-12 mb-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea></div>
</div>
<div class="d-flex gap-2"><button class="btn btn-primary">Save Lead</button><a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Cancel</a></div>
</form></div></div>
@endsection
