<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Two-Factor Authentication — Global Consultancy</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-white min-h-screen font-sans">
<div class="max-w-md mx-auto p-8 mt-12">
<div class="bg-white rounded-lg shadow-lg p-8">
<h2 class="text-2xl font-bold text-gray-900 mb-2 text-center">Two-Factor Authentication</h2>
<p class="text-sm text-gray-600 text-center mb-6">Enter the 6-digit code from your authenticator app. Recovery codes also work.</p>
@if($errors->any())<div class="bg-red-100 border-red-400 text-red-700 px-4 py-3 rounded mb-4"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('2fa.verify') }}">@csrf
<div class="mb-6"><label class="block text-gray-700 text-sm font-bold mb-2" for="code">Authentication code</label>
<input type="text" name="code" id="code" inputmode="numeric" autocomplete="one-time-code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 text-center text-2xl tracking-widest" required autofocus></div>
<button type="submit" class="w-full bg-blue-600 text-white font-medium py-2 px-4 rounded hover:bg-blue-700">Verify</button>
</form>
</div></div>
</body>
</html>
