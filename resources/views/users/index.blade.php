@extends('layouts.app')
@section('title','Users')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h4 class="mb-0">Users</h4><button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#userCreate"><i class="fa-solid fa-plus me-1"></i>New User</button></div>
<div class="card shadow-sm"><div class="card-body">
<x-datatable id="usersTable">
<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
<tbody>@forelse(($users ?? []) as $u)<tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td><x-status-badge :status="$u->role ?? ''"/></td>
<td class="text-nowrap"><button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#userEdit{{ $u->id }}">Edit</button><form method="POST" action="{{ route('users.destroy', $u) }}" class="d-inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr>
@empty @endforelse</tbody>
</x-datatable>
</div></div>
<x-modal id="userCreate" title="New User">
<form method="POST" action="{{ route('users.store') }}">@csrf @include('users._form')<button class="btn btn-primary">Create</button></form>
</x-modal>
@foreach(($users ?? []) as $u)
<x-modal id="userEdit{{ $u->id }}" title="Edit User">
<form method="POST" action="{{ route('users.update', $u) }}">@csrf @method('PUT') @include('users._form',['user'=>$u])<button class="btn btn-primary">Update</button></form>
</x-modal>
@endforeach
@endsection
