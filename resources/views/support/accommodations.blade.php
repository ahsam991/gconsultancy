@extends('layouts.app')
@section('title','Accommodation Support')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Accommodation Support</h4>
</div>
<div class="card shadow-sm mb-3"><div class="card-body">
<form method="GET" action="{{ Route::has('support.accommodations') ? route('support.accommodations') : url('/support/accommodations') }}" class="row g-2 align-items-end">
    <div class="col-md-6">
        <label class="form-label small" for="filter_candidate">Candidate</label>
        <select name="candidate_id" id="filter_candidate" class="form-select" onchange="this.form.submit()">
            <option value="">All candidates</option>
            @foreach(($candidates ?? []) as $c)
            <option value="{{ $c->id }}" @selected(request('candidate_id') == $c->id)>{{ $c->first_name }} {{ $c->last_name }} ({{ $c->uid }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 d-flex gap-1">
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ Route::has('support.accommodations') ? route('support.accommodations') : url('/support/accommodations') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>
</div></div>

<div class="card shadow-sm mb-3">
<div class="card-header fw-semibold">Add Accommodation</div>
<div class="card-body">
<form method="POST" action="{{ Route::has('support.accommodations.store') ? route('support.accommodations.store') : url('/support/accommodations') }}">
    @csrf
    <div class="row">
        <div class="col-md-4 mb-3">
            <label class="form-label" for="candidate_id">Candidate <span class="text-danger">*</span></label>
            <select name="candidate_id" id="candidate_id" class="form-select @error('candidate_id') is-invalid @enderror" required>
                <option value="">Select candidate</option>
                @foreach(($candidates ?? []) as $c)
                <option value="{{ $c->id }}" @selected(old('candidate_id', request('candidate_id')) == $c->id)>{{ $c->first_name }} {{ $c->last_name }}</option>
                @endforeach
            </select>
            @error('candidate_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="application_id">Application</label>
            <select name="application_id" id="application_id" class="form-select @error('application_id') is-invalid @enderror">
                <option value="">None</option>
                @foreach(($applications ?? []) as $a)
                <option value="{{ $a->id }}" @selected(old('application_id') == $a->id)>{{ $a->uid }} — {{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</option>
                @endforeach
            </select>
            @error('application_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="provider">Provider</label>
            <input name="provider" id="provider" value="{{ old('provider') }}" class="form-control @error('provider') is-invalid @enderror" placeholder="e.g. Unite Students" maxlength="255">
            @error('provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="property">Property</label>
            <input name="property" id="property" value="{{ old('property') }}" class="form-control @error('property') is-invalid @enderror" placeholder="e.g. St Pancras Way Hall" maxlength="255">
            @error('property')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="room_type">Room Type</label>
            <input name="room_type" id="room_type" value="{{ old('room_type') }}" class="form-control @error('room_type') is-invalid @enderror" placeholder="e.g. En-suite single" maxlength="100">
            @error('room_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4 mb-3">
            <label class="form-label" for="location">Location</label>
            <input name="location" id="location" value="{{ old('location') }}" class="form-control @error('location') is-invalid @enderror" placeholder="e.g. London NW1" maxlength="255">
            @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="price">Price</label>
            <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" class="form-control @error('price') is-invalid @enderror">
            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="currency">Currency</label>
            <input name="currency" id="currency" value="{{ old('currency', 'GBP') }}" class="form-control @error('currency') is-invalid @enderror" maxlength="10">
            @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="deposit">Deposit</label>
            <input type="number" step="0.01" min="0" name="deposit" id="deposit" value="{{ old('deposit') }}" class="form-control @error('deposit') is-invalid @enderror">
            @error('deposit')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label" for="booking_status">Booking Status</label>
            <select name="booking_status" id="booking_status" class="form-select @error('booking_status') is-invalid @enderror">
                @foreach(['enquiry','booked','confirmed','cancelled'] as $bs)
                <option value="{{ $bs }}" @selected(old('booking_status') === $bs)>{{ ucfirst($bs) }}</option>
                @endforeach
            </select>
            @error('booking_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="check_in">Check-in</label>
            <input type="date" name="check_in" id="check_in" value="{{ old('check_in') }}" class="form-control @error('check_in') is-invalid @enderror">
            @error('check_in')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-6 mb-3">
            <label class="form-label" for="check_out">Check-out</label>
            <input type="date" name="check_out" id="check_out" value="{{ old('check_out') }}" class="form-control @error('check_out') is-invalid @enderror">
            @error('check_out')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Add Accommodation</button>
</form>
</div>
</div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-hover align-middle">
<thead><tr><th>Candidate</th><th>Provider / Property</th><th>Room</th><th>Price</th><th>Check-in → Out</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
<tbody>
@forelse(($accommodations ?? []) as $acc)
<tr>
    <td>{{ $acc->candidate->first_name ?? '' }} {{ $acc->candidate->last_name ?? '' }}</td>
    <td>{{ $acc->provider ?? '—' }}<br><span class="small text-muted">{{ $acc->property ?? '' }} {{ $acc->location ? '· '.$acc->location : '' }}</span></td>
    <td>{{ $acc->room_type ?? '—' }}</td>
    <td>{{ $acc->currency ?? '' }} {{ $acc->price !== null ? number_format($acc->price, 2) : '—' }}</td>
    <td class="small">{{ $acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('d M Y') : '—' }} → {{ $acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('d M Y') : '—' }}</td>
    <td><span class="badge bg-secondary">{{ $acc->booking_status ?? 'enquiry' }}</span></td>
    <td class="text-end text-nowrap">
        <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#accEdit{{ $acc->id }}">Edit</button>
        @if(Route::has('support.accommodations.destroy'))
        <form method="POST" action="{{ route('support.accommodations.destroy', $acc->id) }}" class="d-inline" onsubmit="return confirm('Delete this accommodation?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form>
        @endif
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">No accommodation records found.</td></tr>
@endforelse
</tbody>
</table></div>
{{ ($accommodations ?? null)?->links() }}
</div></div>

@foreach(($accommodations ?? []) as $acc)
<div class="modal fade" id="accEdit{{ $acc->id }}" tabindex="-1" aria-labelledby="accEditLabel{{ $acc->id }}" aria-hidden="true">
<div class="modal-dialog modal-lg"><div class="modal-content">
<div class="modal-header"><h5 class="modal-title" id="accEditLabel{{ $acc->id }}">Edit Accommodation</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
<form method="POST" action="{{ Route::has('support.accommodations.update') ? route('support.accommodations.update', $acc->id) : url('/support/accommodations/'.$acc->id) }}">
@csrf @method('PUT')
<div class="modal-body">
    <div class="row">
        <div class="col-md-6 mb-3"><label class="form-label">Provider</label><input name="provider" value="{{ old('provider', $acc->provider) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Property</label><input name="property" value="{{ old('property', $acc->property) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Room Type</label><input name="room_type" value="{{ old('room_type', $acc->room_type) }}" class="form-control" maxlength="100"></div>
        <div class="col-md-6 mb-3"><label class="form-label">Location</label><input name="location" value="{{ old('location', $acc->location) }}" class="form-control" maxlength="255"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Price</label><input type="number" step="0.01" min="0" name="price" value="{{ old('price', $acc->price) }}" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Currency</label><input name="currency" value="{{ old('currency', $acc->currency) }}" class="form-control" maxlength="10"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Deposit</label><input type="number" step="0.01" min="0" name="deposit" value="{{ old('deposit', $acc->deposit) }}" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Check-in</label><input type="date" name="check_in" value="{{ old('check_in', $acc->check_in ? \Carbon\Carbon::parse($acc->check_in)->format('Y-m-d') : '') }}" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Check-out</label><input type="date" name="check_out" value="{{ old('check_out', $acc->check_out ? \Carbon\Carbon::parse($acc->check_out)->format('Y-m-d') : '') }}" class="form-control"></div>
        <div class="col-md-4 mb-3"><label class="form-label">Booking Status</label>
            <select name="booking_status" class="form-select">
                @foreach(['enquiry','booked','confirmed','cancelled'] as $bs)
                <option value="{{ $bs }}" @selected(old('booking_status', $acc->booking_status) === $bs)>{{ ucfirst($bs) }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>
<div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save changes</button></div>
</form>
</div></div>
</div>
@endforeach
@endsection
