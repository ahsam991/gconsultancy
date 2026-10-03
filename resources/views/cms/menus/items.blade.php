@extends('layouts.app')
@section('title', 'Menu Items')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Items: {{ $menu->name }} <small class="text-muted">({{ $menu->location }})</small></h4>
    <a href="{{ Route::has('cms.menus.index') ? route('cms.menus.index') : url('/crm/cms/menus') }}" class="btn btn-sm btn-outline-secondary">Back to Menus</a>
</div>
@include('cms.menus._items', ['menu' => $menu, 'parents' => $parents ?? []])
@endsection
