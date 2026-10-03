@extends('layouts.app')
@section('title', 'My Availability')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Staff Availability</h4>
    @if(Route::has('availabilities.store'))
        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#availabilityCreate"><i class="fa-solid fa-plus me-1"></i>Add Window</button>
    @endif
</div>
@if(in_array(auth()->user()?->role?->name, ['admin', 'manager'], true) && Route::has('availabilities.index'))
    <x-filter-panel>
        <form method="GET" action="{{ route('availabilities.index') }}" class="row g-2 align-items-end w-100">
            <div class="col-md-4">
                <label class="form-label small">Staff member</label>
                <select name="staff_id" class="form-select">
                    <option value="">All staff</option>
                    @foreach(($staff ?? []) as $member)
                        <option value="{{ $member->id }}" @selected((string) request('staff_id') === (string) $member->id)>{{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-1">
                <button class="btn btn-primary">Filter</button>
                <a href="{{ route('availabilities.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>
    </x-filter-panel>
@endif
<div class="card shadow-sm">
    <div class="card-body">
        <x-datatable id="availabilityTable">
            <thead><tr><th>Staff</th><th>Day</th><th>Start</th><th>End</th><th>Available</th><th>Branch</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse(($availabilities ?? []) as $availability)
                    <tr>
                        <td>{{ $availability->user->name ?? '—' }}</td>
                        <td>{{ ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$availability->day_of_week] ?? $availability->day_of_week }}</td>
                        <td>{{ substr((string) $availability->start_time, 0, 5) }}</td>
                        <td>{{ substr((string) $availability->end_time, 0, 5) }}</td>
                        <td><x-status-badge :status="$availability->is_available ? 'yes' : 'no'" /></td>
                        <td>{{ $availability->branch->name ?? '—' }}</td>
                        <td class="text-nowrap">
                            @if(Route::has('availabilities.update'))
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#availabilityEdit{{ $availability->id }}">Edit</button>
                            @endif
                            @if(Route::has('availabilities.destroy'))
                                <form method="POST" action="{{ route('availabilities.destroy', $availability) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-muted">No availability windows yet.</td></tr>
                @endforelse
            </tbody>
        </x-datatable>
    </div>
</div>
@if(isset($availabilities) && method_exists($availabilities, 'links'))
    <div class="mt-3">{{ $availabilities->links() }}</div>
@endif
@if(Route::has('availabilities.store'))
    <x-modal id="availabilityCreate" title="Add Availability">
        <form method="POST" action="{{ route('availabilities.store') }}">
            @csrf
            @include('availabilities._form')
            <button class="btn btn-primary">Save</button>
        </form>
    </x-modal>
@endif
@foreach(($availabilities ?? []) as $availability)
    @if(Route::has('availabilities.update'))
        <x-modal id="availabilityEdit{{ $availability->id }}" title="Edit Availability">
            <form method="POST" action="{{ route('availabilities.update', $availability) }}">
                @csrf @method('PUT')
                @include('availabilities._form', ['availability' => $availability])
                <button class="btn btn-primary">Update</button>
            </form>
        </x-modal>
    @endif
@endforeach
@endsection
