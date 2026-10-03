@extends('layouts.app')
@section('title','My tasks')
@section('content')
<h4 class="mb-3 text-capitalize">My tasks</h4>
<div class="card shadow-sm"><div class="card-body">
@forelse(($tasks ?? []) as $r)<div class="border-bottom py-2">{{ $r->title ?? $r->name ?? ('#'. $r->id) }}</div>
@empty<x-empty-state title="No tasks" message="Nothing to show yet."/>@endforelse
</div></div>
@endsection
