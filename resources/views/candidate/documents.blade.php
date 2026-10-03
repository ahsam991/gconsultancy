@extends('layouts.app')
@section('title','My documents')
@section('content')
<h4 class="mb-3 text-capitalize">My documents</h4>
<div class="card shadow-sm"><div class="card-body">
@forelse(($documents ?? []) as $r)<div class="border-bottom py-2">{{ $r->title ?? $r->name ?? ('#'. $r->id) }}</div>
@empty<x-empty-state title="No documents" message="Nothing to show yet."/>@endforelse
</div></div>
@endsection
