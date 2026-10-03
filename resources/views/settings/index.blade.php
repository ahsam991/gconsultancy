@extends('layouts.app')
@section('title','Settings')
@section('content')
<h4 class="mb-3">Settings</h4>
<form method="POST" action="{{ route('settings.update') }}">@csrf @method('PUT')
@foreach(($groups ?? ['General' => ($settings ?? [])]) as $group => $items)
<div class="card shadow-sm mb-3"><div class="card-header fw-semibold">{{ $group }}</div><div class="card-body row">
@forelse($items as $k => $v)
<div class="col-md-6 mb-3"><label class="form-label small">{{ ucwords(str_replace('_',' ',$k)) }}</label><input name="settings[{{ $k }}]" value="{{ old('settings.'.$k, is_string($v) ? $v : ($v->value ?? '')) }}" class="form-control"></div>
@empty
@endforelse
@if(empty($items))
<div class="col-md-6 mb-3"><label class="form-label small">Organisation Name</label><input name="settings[org_name]" value="{{ old('settings.org_name', $settings['org_name'] ?? 'Global Consultancy') }}" class="form-control"></div>
<div class="col-md-6 mb-3"><label class="form-label small">Support Email</label><input name="settings[support_email]" value="{{ old('settings.support_email', $settings['support_email'] ?? '') }}" class="form-control"></div>
@endif
</div></div>
@endforeach
<button class="btn btn-primary">Save Settings</button>
</form>
@endsection
