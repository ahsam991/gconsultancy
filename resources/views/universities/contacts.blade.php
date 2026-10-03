@extends('layouts.app')
@section('title', 'University Contacts')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Contacts: {{ $university->name }}</h4>
    <a href="{{ Route::has('universities.show') ? route('universities.show', $university) : url('/crm/universities/' . $university->id) }}" class="btn btn-sm btn-outline-secondary">Back</a>
</div>

<div class="card shadow-sm mb-3"><div class="card-body">
    <h6>Add Contact</h6>
    <form method="POST" action="{{ Route::has('universities.contacts.store') ? route('universities.contacts.store', $university) : url('/crm/universities/' . $university->id . '/contacts') }}" class="row g-2 align-items-end">
        @csrf
        <div class="col-md-3">
            <label class="form-label small" for="name">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required maxlength="255">
            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="position">Position</label>
            <input type="text" name="position" id="position" value="{{ old('position') }}" class="form-control" maxlength="255">
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="department">Department</label>
            <input type="text" name="department" id="department" value="{{ old('department') }}" class="form-control" maxlength="255">
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" maxlength="255">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label small" for="phone">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="form-control" maxlength="50">
        </div>
        <div class="col-md-1">
            <div class="form-check mt-4">
                <input type="checkbox" name="is_primary" id="is_primary" value="1" class="form-check-input" @checked(old('is_primary'))>
                <label class="form-check-label small" for="is_primary">Primary</label>
            </div>
        </div>
        <div class="col-12"><button class="btn btn-primary btn-sm">Add Contact</button></div>
    </form>
</div></div>

<div class="card shadow-sm"><div class="card-body">
<div class="table-responsive"><table class="table table-striped">
<thead><tr><th>Name</th><th>Position</th><th>Email</th><th>Phone</th><th>Primary</th><th></th></tr></thead>
<tbody>
@forelse(($contacts ?? []) as $contact)
<tr>
    <td>{{ $contact->name }}</td>
    <td>{{ $contact->position ?? '—' }}</td>
    <td class="small">{{ $contact->email ?? '—' }}</td>
    <td class="small">{{ $contact->phone ?? '—' }}</td>
    <td>{{ $contact->is_primary ? 'Yes' : 'No' }}</td>
    <td class="text-nowrap">
        <form method="POST" action="{{ Route::has('universities.contacts.update') ? route('universities.contacts.update', [$university, $contact]) : url('/crm/universities/' . $university->id . '/contacts/' . $contact->id) }}" class="d-inline">
            @csrf @method('PUT')
            <input type="hidden" name="name" value="{{ $contact->name }}">
            <input type="hidden" name="position" value="{{ $contact->position }}">
            <input type="hidden" name="department" value="{{ $contact->department }}">
            <input type="hidden" name="email" value="{{ $contact->email }}">
            <input type="hidden" name="phone" value="{{ $contact->phone }}">
            <input type="hidden" name="is_primary" value="{{ $contact->is_primary ? 0 : 1 }}">
            <button class="btn btn-sm btn-outline-secondary">{{ $contact->is_primary ? 'Unset' : 'Primary' }}</button>
        </form>
        <form method="POST" action="{{ Route::has('universities.contacts.destroy') ? route('universities.contacts.destroy', [$university, $contact]) : url('/crm/universities/' . $university->id . '/contacts/' . $contact->id) }}" class="d-inline" onsubmit="return confirm('Delete?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="6" class="text-center text-muted">No contacts yet. Add the first one above.</td></tr>
@endforelse
</tbody>
</table></div>
@if(isset($contacts) && method_exists($contacts, 'links')){{ $contacts->links() }}@endif
</div></div>
@endsection
