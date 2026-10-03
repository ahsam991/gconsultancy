@extends('layouts.app')
@section('title','Leads')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Leads</h4><a href="{{ route('leads.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>Add Lead</a></div>
<x-filter-panel>
<form method="GET" action="{{ route('leads.index') }}" class="row g-2 align-items-end w-100">
    <div class="col-md-3"><label class="form-label small">Search</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Name, email, phone"></div>
    <div class="col-md-3"><label class="form-label small">Source</label><select name="source" class="form-select"><option value="">All</option>@foreach(['Walk-in','Referral','Website','Social Media','Agent','Event'] as $s)<option @selected(request('source')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label small">Status</label><select name="status" class="form-select"><option value="">All</option>@foreach(['new','contacted','qualified','converted','lost'] as $s)<option @selected(request('status')==$s)>{{ $s }}</option>@endforeach</select></div>
    <div class="col-md-3 d-flex gap-1"><button class="btn btn-primary">Filter</button><a href="{{ route('leads.index') }}" class="btn btn-outline-secondary">Reset</a></div>
</form>
</x-filter-panel>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="leadsTable">
<thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Source</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($leads ?? []) as $l)<tr><td><a href="{{ route('leads.show', $l) }}">{{ $l->first_name ?? '' }} {{ $l->last_name ?? $l->name ?? '' }}</a></td><td>{{ $l->email ?? '—' }}</td><td>{{ $l->phone ?? '—' }}</td><td>{{ $l->source->name ?? '—' }}</td><td><x-status-badge :status="$l->status ?? 'new'"/></td>
<td class="text-nowrap"><a href="{{ route('leads.show', $l) }}" class="btn btn-sm btn-outline-info">View</a><form method="POST" action="{{ route('leads.convert', $l) }}" class="d-inline">@csrf<button class="btn btn-sm btn-outline-success">Convert</button></form><form method="POST" action="{{ route('leads.destroy', $l) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
