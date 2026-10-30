<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title>@yield('title', 'Dasbor') · CMS {{ config('site.short') }}</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600..800&family=Geist:wght@400..700&family=Geist+Mono:wght@500;600&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/admin.css') }}">
</head>
<body>
@php
    $newCount = \App\Models\Booking::where('status', 'baru')->count();
    $nav = [
        'Beranda' => [
            ['admin.dashboard', 'Dasbor', 'home', 'admin.dashboard'],
        ],
        'Operasional' => [
            ['admin.bookings.index', 'Permintaan masuk', 'inbox', 'admin.bookings.*'],
            ['admin.tickets.index', 'Tiket servis', 'ticket', 'admin.tickets.*'],
            ['admin.prices.index', 'Daftar harga', 'tag', 'admin.prices.*'],
        ],
        'Konten situs' => [
            ['admin.articles.index', 'Artikel', 'doc', 'admin.articles.*'],
            ['admin.services.index', 'Layanan', 'wrench', 'admin.services.*'],
            ['admin.segments', 'Tab beranda', 'tabs', 'admin.segments*'],
            ['admin.landings.index', 'Landing page iklan', 'rocket', 'admin.landings.*'],
            ['admin.profile', 'Profil & kontak', 'building', 'admin.profile*'],
            ['admin.lists', 'Klien, wilayah, langkah', 'list', 'admin.lists*'],
        ],
        'Akun' => [
            ['admin.account', 'Akun saya', 'user', 'admin.account*'],
            ['admin.users.index', 'Daftar admin', 'users', 'admin.users.*'],
        ],
    ];
@endphp
<div class="adm">
  <aside class="side" id="side">
    <a class="side-brand" href="{{ route('admin.dashboard') }}"><b>DS</b><span>{{ config('site.short') }}<small>CMS</small></span></a>
    <nav class="nav" aria-label="Menu admin">
      @foreach ($nav as $group => $items)
        <h4>{{ $group }}</h4>
        @foreach ($items as [$route, $label, $icon, $pattern])
          <a href="{{ route($route) }}" @class(['on' => request()->routeIs($pattern)])>
            @include('admin.partials.icon', ['name' => $icon])
            {{ $label }}
            @if ($route === 'admin.bookings.index' && $newCount)<span class="badge-n">{{ $newCount }}</span>@endif
          </a>
        @endforeach
      @endforeach
      <h4>Situs</h4>
      <a href="{{ route('home') }}" target="_blank" rel="noopener">@include('admin.partials.icon', ['name' => 'external'])Lihat situs</a>
      <form method="post" action="{{ route('admin.logout') }}">@csrf
        <button type="submit">@include('admin.partials.icon', ['name' => 'logout'])Keluar</button>
      </form>
    </nav>
    <div class="side-user"><strong>{{ auth()->user()->name }}</strong>{{ auth()->user()->email }}</div>
  </aside>

  <div class="main">
    <header class="top">
      <button class="burger" id="burger" type="button" aria-label="Buka menu">@include('admin.partials.icon', ['name' => 'menu'])</button>
      <h1>@yield('heading', 'Dasbor')</h1>
      <div class="acts">@yield('actions')</div>
    </header>
    <main class="content">
      @if (session('ok'))<p class="flash" role="status">{{ session('ok') }}</p>@endif
      @if ($errors->any())
        <div class="flash err" role="alert">
          @if ($errors->count() === 1){{ $errors->first() }}@else<ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif
        </div>
      @endif
      @yield('content')
    </main>
  </div>
</div>
<script src="{{ \App\Support\Site::asset('js/admin.js') }}" defer></script>
</body>
</html>
