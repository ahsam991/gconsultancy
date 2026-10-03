@extends('layouts.app')
@section('title', 'My Profile')
@section('breadcrumb', 'Portal Profile')
@section('content')
<div class="container-fluid">
    <h1 class="h4">My profile</h1>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
    <form action="{{ route('portal.profile.update') }}" method="POST" class="card card-body mb-3">
        @csrf @method('PUT')
        <div class="row g-2">
            <div class="col-6"><label class="form-label" for="p-first">First name</label><input id="p-first" name="first_name" value="{{ old('first_name', $candidate->first_name ?? '') }}" class="form-control form-control-lg" required></div>
            <div class="col-6"><label class="form-label" for="p-last">Last name</label><input id="p-last" name="last_name" value="{{ old('last_name', $candidate->last_name ?? '') }}" class="form-control form-control-lg" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="p-phone">Phone</label><input id="p-phone" name="phone" value="{{ old('phone', $candidate->phone ?? '') }}" class="form-control form-control-lg" required></div>
            <div class="col-12 col-md-6"><label class="form-label" for="p-dob">Date of birth</label><input id="p-dob" name="dob" type="date" value="{{ old('dob', $candidate->dob?->format('Y-m-d') ?? '') }}" class="form-control form-control-lg"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="p-nat">Nationality</label><input id="p-nat" name="nationality" value="{{ old('nationality', $candidate->nationality ?? '') }}" class="form-control form-control-lg"></div>
            <div class="col-12 col-md-6"><label class="form-label" for="p-pass">Passport no</label><input id="p-pass" name="passport_no" value="{{ old('passport_no', $candidate->passport_no ?? '') }}" class="form-control form-control-lg"></div>
            <div class="col-12"><label class="form-label" for="p-dest">Preferred destination</label><input id="p-dest" name="preferred_destination" value="{{ old('preferred_destination', $candidate->preferred_destination ?? '') }}" class="form-control form-control-lg"></div>
            <div class="col-12"><label class="form-label" for="p-addr">Address</label><textarea id="p-addr" name="address" rows="2" class="form-control form-control-lg">{{ old('address', $candidate->address ?? '') }}</textarea></div>
        </div>
        <button class="btn btn-primary btn-lg w-100 mt-3" type="submit">Save profile</button>
    </form>

    <h2 class="h5">Academics</h2>
    <div class="table-responsive"><table class="table table-bordered">
        <thead><tr><th>Level</th><th>Institution</th><th>Year</th><th>Result</th></tr></thead>
        <tbody>
            @forelse(($candidate->qualifications ?? []) as $q)
                <tr><td>{{ $q->level ?? '' }}</td><td>{{ $q->institution ?? '' }}</td><td>{{ $q->passing_year ?? '' }}</td><td>{{ $q->result ?? '' }}</td></tr>
            @empty
                <tr><td colspan="4" class="text-muted">No academic records yet.</td></tr>
            @endforelse
        </tbody>
    </table></div>

    <h2 class="h5 mt-3">English test</h2>
    <div class="table-responsive"><table class="table table-bordered">
        <thead><tr><th>Test</th><th>Overall</th><th>Date</th></tr></thead>
        <tbody>
            @forelse(($candidate->englishTests ?? []) as $e)
                <tr><td>{{ $e->test_type ?? '' }}</td><td>{{ $e->overall ?? '' }}</td><td>{{ $e->test_date ?? '' }}</td></tr>
            @empty
                <tr><td colspan="3" class="text-muted">No English test on file.</td></tr>
            @endforelse
        </tbody>
    </table></div>
</div>
@endsection
