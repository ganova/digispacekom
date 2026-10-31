<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Akses ditolak · {{ config('site.short') }}</title>
@php($fav = \App\Support\Site::favicon())
<link rel="icon" href="{{ $fav['url'] }}" type="{{ $fav['type'] }}">
@if ($fav['custom'])<link rel="apple-touch-icon" href="{{ $fav['url'] }}">@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600..800&family=Geist:wght@400..700&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/admin.css') }}">
</head>
<body class="login">
<span class="orb o1"></span><span class="orb o2"></span><span class="mesh"></span>
<main class="login-card" style="text-align:center">
  <div class="brand" style="justify-content:center"><span class="logo-mark">DS</span>{{ config('site.short') }}</div>
  <h1>Akses <em>ditolak</em></h1>
  <p class="lead" style="margin-bottom:24px">{{ $exception->getMessage() ?: 'Akun Anda tidak punya izin untuk membuka halaman ini.' }}</p>
  <a class="btn btn-primary btn-block" href="{{ auth()->check() ? route('admin.dashboard') : route('home') }}">{{ auth()->check() ? 'Kembali ke dasbor' : 'Ke beranda' }}</a>
</main>
</body>
</html>
