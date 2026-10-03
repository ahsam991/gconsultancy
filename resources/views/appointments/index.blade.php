@extends('layouts.app')
@section('title','Appointments')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Appointments</h4><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#apptCreate"><i class="fa-solid fa-plus me-1"></i>New Appointment</button></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="apptTable">
<thead><tr><th>When</th><th>Title</th><th>Candidate</th><th>Type</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($appointments ?? []) as $a)<tr><td>{{ isset($a->scheduled_at) ? \Carbon\Carbon::parse($a->scheduled_at)->format('d M Y H:i') : '—' }}</td><td>{{ $a->title }}</td><td>{{ $a->candidate->first_name ?? '' }} {{ $a->candidate->last_name ?? '' }}</td><td>{{ $a->type ?? '—' }}</td><td><x-status-badge :status="$a->status ?? ''"/></td>
<td class="text-nowrap"><button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#apptEdit{{ $a->id }}">Edit</button><form method="POST" action="{{ route('appointments.destroy', $a) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty @endforelse</tbody>
</x-datatable>
</div></div>
<x-modal id="apptCreate" title="New Appointment">
<form method="POST" action="{{ route('appointments.store') }}">@csrf @include('appointments._form')<button class="btn btn-primary">Create</button></form>
</x-modal>
@foreach(($appointments ?? []) as $a)
<x-modal id="apptEdit{{ $a->id }}" title="Edit Appointment">
<form method="POST" action="{{ route('appointments.update', $a) }}">@csrf @method('PUT') @include('appointments._form',['appointment'=>$a])<button class="btn btn-primary">Update</button></form>
</x-modal>
@endforeach
@endsection
