@extends('layouts.app')
@section('title','Invoices')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Invoices</h4><a href="{{ route('invoices.create') }}" class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i>New Invoice</a></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="invTable">
<thead><tr><th>No</th><th>University</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>@forelse(($invoices ?? []) as $i)<tr><td>{{ $i->invoice_no ?? $i->id }}</td><td>{{ $i->university->name ?? '—' }}</td><td>£{{ number_format($i->total ?? 0,2) }}</td><td><x-status-badge :status="$i->status ?? 'unpaid'"/></td>
<td class="text-nowrap"><a href="{{ route('invoices.show', $i) }}" class="btn btn-sm btn-outline-info">View</a><a href="{{ route('invoices.pdf', $i) }}" class="btn btn-sm btn-outline-secondary">PDF</a></td></tr>@empty @endforelse</tbody>
</x-datatable>
</div></div>
@endsection
