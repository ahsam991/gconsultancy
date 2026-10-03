@extends('layouts.app')
@section('title','Enable Two-Factor Authentication')
@section('content')
<h4 class="mb-3">Enable Two-Factor Authentication</h4>
@if(session('recovery_codes'))
<div class="alert alert-warning"><strong>Save these recovery codes now — each works once:</strong><ul class="mb-0">@foreach(session('recovery_codes') as $c)<li><code>{{ $c }}</code></li>@endforeach</ul></div>
@endif
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<div class="row"><div class="col-md-6"><div class="card shadow-sm"><div class="card-body text-center">
<p class="small text-muted">1. Scan with Google Authenticator / Authy</p>
<div class="d-inline-block border p-2 bg-white">{!! $qrSvg !!}</div>
<p class="small text-muted mt-2">2. Or enter manually: <code>{{ $secret }}</code></p>
</div></div></div>
<div class="col-md-6"><div class="card shadow-sm"><div class="card-body">
<p class="small text-muted">3. Enter the 6-digit code to confirm</p>
<form method="POST" action="{{ route('2fa.confirm') }}">@csrf
<div class="mb-3"><input name="code" inputmode="numeric" class="form-control form-control-lg text-center" required></div>
<button class="btn btn-primary w-100">Confirm & Enable</button>
</form>
</div></div></div></div>
@endsection
