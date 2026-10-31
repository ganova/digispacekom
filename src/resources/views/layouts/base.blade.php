<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title>@hasSection('title')@yield('title') · {{ config('site.short') }}@else{{ config('site.name') }} · {{ config('site.tagline') }}@endif</title>
<meta name="description" content="@yield('description', 'IT support, service komputer, printer, dan jaringan untuk kantor, sekolah, instansi, dan rumah se-Jabodetabek. Teknisi datang ke lokasi.')">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('site.name') }}">
<meta property="og:title" content="@hasSection('title')@yield('title')@else{{ config('site.name') }}@endif">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="theme-color" content="#070E24">
<meta name="color-scheme" content="light dark">
<script>
  /* Tema: Otomatis (gelap pukul 18.00–05.59 waktu pengunjung), Terang, atau Gelap. Dijalankan sebelum CSS agar tidak berkedip. */
  (function(){var m='auto';try{m=localStorage.getItem('site-theme')||'auto'}catch(e){}
  var h=new Date().getHours(),d=m==='dark'||(m==='auto'&&(h>=18||h<6)),r=document.documentElement;
  r.dataset.theme=d?'dark':'light';r.dataset.themeMode=m})();
</script>
@stack('meta')
@php($fav = \App\Support\Site::favicon())
<link rel="icon" href="{{ $fav['url'] }}" type="{{ $fav['type'] }}">
@if ($fav['custom'])<link rel="apple-touch-icon" href="{{ $fav['url'] }}">@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500..800&family=Geist:wght@400..700&family=Geist+Mono:wght@500;600&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/base.css') }}">
@stack('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/dark.css') }}">
<script>document.documentElement.classList.add('js')</script>
</head>
<body class="@yield('body_class')" data-hero="@yield('hero_tone', 'light')">
<a class="skip" href="#main">Langsung ke isi</a>
<div class="progress" aria-hidden="true"></div>
@yield('body')
<a class="wa-float" href="{{ \App\Support\Site::whatsappUrl('Halo Digital Space Komputer, saya mau tanya servis.') }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
  <span class="wa-ic">@include('partials.icon', ['name' => 'wa'])</span><em>@yield('wa_label', 'Chat teknisi sekarang')</em>
</a>
<script src="{{ \App\Support\Site::asset('js/site.js') }}" defer></script>
</body>
</html>
