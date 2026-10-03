@extends('layouts.app')
@section('title','Tasks')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Tasks</h4><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#taskCreate"><i class="fa-solid fa-plus me-1"></i>New Task</button></div>
<x-filter-panel>
<form method="GET" action="{{ route('tasks.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['pending','in_progress','completed','overdue','cancelled'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Priority</label><select name="priority" class="form-select"><option value="">All</option>@foreach(['low','normal','high','urgent'] as $p)<option @selected(request('priority')==$p)>{{ $p }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Assignee</label><select name="assigned_to" class="form-select"><option value="">All</option>@foreach(($staff ?? $users ?? []) as $u)<option value="{{ $u->id }}" @selected(request('assigned_to')==$u->id)>{{ $u->name }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="tasksTable">
<thead><tr><th>Title</th><th>Status</th><th>Priority</th><th>Due</th><th>Assignee</th><th>Actions</th></tr></thead>
<tbody>@forelse(($tasks ?? []) as $t)<tr><td>{{ $t->title }}</td><td><x-status-badge :status="$t->status ?? ''"/></td><td><x-status-badge :status="$t->priority ?? ''"/></td><td>{{ $t->due_date ?? '—' }}</td><td>{{ $t->assignee->name ?? '—' }}</td>
<td class="text-nowrap"><button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#taskEdit{{ $t->id }}">Edit</button><form method="POST" action="{{ route('tasks.destroy', $t) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty @endforelse</tbody>
</x-datatable>
</div></div>
<x-modal id="taskCreate" title="New Task">
<form method="POST" action="{{ route('tasks.store') }}">@csrf @include('tasks._form')<button class="btn btn-primary">Create</button></form>
</x-modal>
@foreach(($tasks ?? []) as $t)
<x-modal id="taskEdit{{ $t->id }}" title="Edit Task">
<form method="POST" action="{{ route('tasks.update', $t) }}">@csrf @method('PUT') @include('tasks._form',['task'=>$t])<button class="btn btn-primary">Update</button></form>
</x-modal>
@endforeach
@endsection
