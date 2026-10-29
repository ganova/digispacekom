<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@hasSection('title')@yield('title') · {{ config('site.short') }}@else{{ config('site.name') }} · {{ config('site.tagline') }}@endif</title>
<meta name="description" content="@yield('description', 'IT support, service komputer, printer, dan jaringan untuk kantor, sekolah, instansi, dan rumah se-Jabodetabek. Teknisi datang ke lokasi.')">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('site.name') }}">
<meta property="og:title" content="@hasSection('title')@yield('title')@else{{ config('site.name') }}@endif">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="theme-color" content="#1F3F8F">
@stack('meta')
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?@yield('fonts')&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/base.css') }}">
@stack('styles')
</head>
<body class="@yield('body_class')">
<a class="skip" href="#main">Langsung ke isi</a>
@yield('body')
<a class="wa-float" href="{{ \App\Support\Site::whatsappUrl('Halo Digital Space Komputer, saya mau tanya servis.') }}" target="_blank" rel="noopener" aria-label="Chat WhatsApp">
  @include('partials.icon', ['name' => 'wa'])<em>@yield('wa_label', 'Chat teknisi sekarang')</em>
</a>
<script src="{{ \App\Support\Site::asset('js/site.js') }}" defer></script>
</body>
</html>
