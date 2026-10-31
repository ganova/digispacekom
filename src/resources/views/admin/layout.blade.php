<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<meta name="view-transition" content="same-origin">
<title>@yield('title', 'Dasbor') · CMS {{ config('site.short') }}</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<script>
  // tema & sidebar diterapkan sebelum render agar tidak berkedip
  try {
    const t = localStorage.getItem('adm-theme');
    if (t) document.documentElement.dataset.theme = t;
    if (localStorage.getItem('adm-mini') === '1') document.documentElement.classList.add('side-mini');
  } catch (e) {}
</script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600..800&family=Geist:wght@400..700&family=Geist+Mono:wght@500;600&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/admin.css') }}">
</head>
<body>
@php
    $newCount = \App\Models\Booking::where('status', 'baru')->count();
    $nav = [
        'Ringkasan' => [
            ['admin.dashboard', 'Dasbor', 'home', 'admin.dashboard'],
        ],
        'Operasional' => [
            ['admin.bookings.index', 'Permintaan masuk', 'inbox', 'admin.bookings.*', true],
            ['admin.tickets.index', 'Tiket servis', 'ticket', 'admin.tickets.*'],
            ['admin.prices.index', 'Daftar harga', 'tag', 'admin.prices.*'],
        ],
        'Konten situs' => [
            ['admin.articles.index', 'Artikel', 'doc', 'admin.articles.*'],
            ['admin.services.index', 'Layanan', 'wrench', 'admin.services.*', true],
            ['admin.segments', 'Tab beranda', 'tabs', 'admin.segments*', true],
            ['admin.landings.index', 'Landing page iklan', 'rocket', 'admin.landings.*', true],
            ['admin.clients', 'Klien & logo', 'badge', 'admin.clients*', true],
            ['admin.profile', 'Profil & kontak', 'building', 'admin.profile*', true],
            ['admin.lists', 'Wilayah & langkah', 'list', 'admin.lists*', true],
        ],
        'Akun' => [
            ['admin.account', 'Akun saya', 'user', 'admin.account*'],
            ['admin.users.index', 'Pengguna & peran', 'users', 'admin.users.*', true],
        ],
    ];

    $isAdmin = auth()->user()->isAdmin();
    $nav = collect($nav)
        ->map(fn ($items) => array_values(array_filter($items, fn ($i) => $isAdmin || empty($i[4]))))
        ->filter()
        ->all();

    $group = 'Ringkasan';
    foreach ($nav as $g => $items) {
        foreach ($items as $item) {
            if (request()->routeIs($item[3])) {
                $group = $g;
            }
        }
    }

    // Perintah untuk palet pencarian (Ctrl+K)
    $commands = [];
    foreach ($nav as $g => $items) {
        foreach ($items as [$route, $label, $icon]) {
            $commands[] = ['label' => $label, 'url' => route($route), 'group' => 'Buka halaman', 'icon' => $icon];
        }
    }
    foreach ([
        ['Buat tiket servis', 'admin.tickets.create', 'ticket', false],
        ['Tambah item harga', 'admin.prices.create', 'tag', false],
        ['Tulis artikel', 'admin.articles.create', 'doc', false],
        ['Tambah layanan', 'admin.services.create', 'wrench', true],
        ['Tambah pengguna', 'admin.users.create', 'users', true],
    ] as [$label, $route, $icon, $adminOnly]) {
        if ($adminOnly && ! $isAdmin) {
            continue;
        }
        $commands[] = ['label' => $label, 'url' => route($route), 'group' => 'Buat baru', 'icon' => 'plus'];
    }
    $commands[] = ['label' => 'Lihat situs', 'url' => route('home'), 'group' => 'Situs', 'icon' => 'external', 'blank' => true];
    $commands[] = ['label' => 'Cek Harga (publik)', 'url' => route('prices.index'), 'group' => 'Situs', 'icon' => 'external', 'blank' => true];

    $user = auth()->user();
    $initials = collect(explode(' ', $user->name))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
@endphp

<aside class="side" id="side" aria-label="Menu admin">
  <a class="side-brand" href="{{ route('admin.dashboard') }}"><span class="logo-mark">DS</span><span>{{ config('site.short') }}<small>Content studio</small></span></a>
  <nav class="nav">
    @foreach ($nav as $g => $items)
      <h4>{{ $g }}</h4>
      @foreach ($items as [$route, $label, $icon, $pattern])
        <a href="{{ route($route) }}" data-tip="{{ $label }}" @class(['on' => request()->routeIs($pattern), 'has-new' => $route === 'admin.bookings.index' && $newCount]) @if (request()->routeIs($pattern)) aria-current="page" @endif>
          @include('admin.partials.icon', ['name' => $icon])
          <span class="lbl">{{ $label }}</span>
          @if ($route === 'admin.bookings.index' && $newCount)<span class="badge-n">{{ $newCount }}</span>@endif
        </a>
      @endforeach
    @endforeach
    <h4>Situs</h4>
    <a href="{{ route('home') }}" target="_blank" rel="noopener" data-tip="Lihat situs">@include('admin.partials.icon', ['name' => 'external'])<span class="lbl">Lihat situs</span></a>
    <form method="post" action="{{ route('admin.logout') }}">@csrf
      <button type="submit" data-tip="Keluar">@include('admin.partials.icon', ['name' => 'logout'])<span class="lbl">Keluar</span></button>
    </form>
  </nav>
  <div class="side-user"><span class="avatar">{{ $initials }}</span><div class="who"><strong>{{ $user->name }}</strong>{{ $isAdmin ? 'Admin penuh' : 'Staf' }}</div></div>
</aside>
<div class="scrim" id="scrim"></div>

<div class="main">
  <header class="top">
    <button class="icon-btn burger" id="burger" type="button" aria-label="Buka menu" aria-controls="side">@include('admin.partials.icon', ['name' => 'menu'])</button>
    <button class="icon-btn collapse-btn" id="collapse" type="button" aria-label="Ringkas menu samping" title="Ringkas menu">@include('admin.partials.icon', ['name' => 'sidebar'])</button>
    <div class="crumb"><span>{{ $group }}</span><i>/</i><b>@yield('heading', 'Dasbor')</b></div>
    <button class="search-pill" id="open-palette" type="button" aria-label="Cari atau lompat ke halaman">
      @include('admin.partials.icon', ['name' => 'search', 'size' => 17])<span>Cari atau lompat ke…</span><kbd>Ctrl</kbd><kbd>K</kbd>
    </button>
    <button class="icon-btn theme-ic" id="theme" type="button" aria-label="Ganti tema terang/gelap" title="Tema terang/gelap">
      <span class="sun">@include('admin.partials.icon', ['name' => 'sun'])</span><span class="moon">@include('admin.partials.icon', ['name' => 'moon'])</span>
    </button>
    <a class="icon-btn site-link" href="{{ route('home') }}" target="_blank" rel="noopener" aria-label="Lihat situs" title="Lihat situs">@include('admin.partials.icon', ['name' => 'external'])</a>
  </header>

  <main class="content" id="content">
    @sectionMissing('no_head')
    <div class="page-head">
      <div>
        <p class="eyebrow">{{ $group }}</p>
        <h1>@yield('heading', 'Dasbor')</h1>
      </div>
      <div class="acts">@yield('actions')</div>
    </div>
    @endif
    @if ($errors->any())
      <div class="flash err" role="alert">
        @include('admin.partials.icon', ['name' => 'alert', 'size' => 18])
        <div>@if ($errors->count() === 1){{ $errors->first() }}@else<ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>@endif</div>
      </div>
    @endif
    @yield('content')
  </main>
</div>

<nav class="bnav" aria-label="Menu cepat">
  <a href="{{ route('admin.dashboard') }}" @class(['on' => request()->routeIs('admin.dashboard')])>@include('admin.partials.icon', ['name' => 'home', 'size' => 20])<span>Dasbor</span></a>
  @if ($isAdmin)
    <a href="{{ route('admin.bookings.index') }}" @class(['on' => request()->routeIs('admin.bookings.*')])>@include('admin.partials.icon', ['name' => 'inbox', 'size' => 20])<span>Masuk</span>@if ($newCount)<i>{{ $newCount }}</i>@endif</a>
  @else
    <a href="{{ route('admin.articles.index') }}" @class(['on' => request()->routeIs('admin.articles.*')])>@include('admin.partials.icon', ['name' => 'doc', 'size' => 20])<span>Artikel</span></a>
  @endif
  <a href="{{ route('admin.tickets.index') }}" @class(['on' => request()->routeIs('admin.tickets.*')])>@include('admin.partials.icon', ['name' => 'ticket', 'size' => 20])<span>Tiket</span></a>
  <a href="{{ route('admin.prices.index') }}" @class(['on' => request()->routeIs('admin.prices.*')])>@include('admin.partials.icon', ['name' => 'tag', 'size' => 20])<span>Harga</span></a>
  <button type="button" data-open-side>@include('admin.partials.icon', ['name' => 'grid', 'size' => 20])<span>Lainnya</span></button>
</nav>

{{-- Toast --}}
<div class="toasts" id="toasts" aria-live="polite">
  @if (session('ok'))
    <div class="toast" role="status"><span class="ok">@include('admin.partials.icon', ['name' => 'check', 'size' => 16])</span><span>{{ session('ok') }}</span><button type="button" aria-label="Tutup">×</button></div>
  @endif
</div>

{{-- Bilah simpan: muncul saat formulir diubah --}}
<div class="savebar" id="savebar" role="region" aria-label="Perubahan belum disimpan">
  <i class="dot"></i><span>Ada perubahan belum disimpan</span>
  <button type="button" class="btn btn-sm btn-ghost" data-discard>Batalkan</button>
  <button type="button" class="btn btn-sm btn-lime" data-save>Simpan <kbd class="hint">Ctrl</kbd><kbd class="hint">S</kbd></button>
</div>

{{-- Dialog konfirmasi --}}
<dialog id="confirm">
  <div class="dlg">
    <div class="ic">@include('admin.partials.icon', ['name' => 'alert', 'size' => 22])</div>
    <h3>Yakin?</h3>
    <p data-msg></p>
    <div class="row">
      <button type="button" class="btn" data-no>Batal</button>
      <button type="button" class="btn btn-danger-solid" data-yes>Ya, lanjutkan</button>
    </div>
  </div>
</dialog>

{{-- Palet perintah --}}
<dialog id="palette" class="palette" aria-label="Cari atau lompat ke halaman">
  <div class="q">@include('admin.partials.icon', ['name' => 'search', 'size' => 19])<input type="text" placeholder="Ketik nama halaman, nomor tiket, atau kata kunci…" autocomplete="off" spellcheck="false" aria-label="Cari"><kbd>Esc</kbd></div>
  <ul role="listbox"></ul>
  <div class="foot"><span><kbd>↑</kbd><kbd>↓</kbd> pilih</span><span><kbd>Enter</kbd> buka</span><span><kbd>Esc</kbd> tutup</span></div>
</dialog>
<script type="application/json" id="commands">{!! json_encode([
    'items' => $commands,
    'search' => array_values(array_filter([
        $isAdmin ? ['label' => 'Cari permintaan', 'url' => route('admin.bookings.index')] : null,
        ['label' => 'Cari tiket', 'url' => route('admin.tickets.index')],
        ['label' => 'Cari harga', 'url' => route('admin.prices.index')],
        ['label' => 'Cari artikel', 'url' => route('admin.articles.index')],
    ])),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
<template id="icons">
  @foreach (['home', 'inbox', 'ticket', 'tag', 'doc', 'wrench', 'tabs', 'rocket', 'building', 'list', 'user', 'users', 'external', 'plus', 'search', 'badge'] as $ic)
    <span data-ic="{{ $ic }}">@include('admin.partials.icon', ['name' => $ic, 'size' => 16])</span>
  @endforeach
</template>
<script src="{{ \App\Support\Site::asset('js/admin.js') }}" defer></script>
</body>
</html>
