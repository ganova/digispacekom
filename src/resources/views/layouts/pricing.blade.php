{{-- Konsep C · Cek Harga & Status — menu terpisah untuk pengunjung yang ingin cek biaya --}}
@extends('layouts.base')

@section('fonts', 'family=Lexend:wght@400;500;600')
@section('body_class', 'cC')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/pricing.css') }}">
@endpush

@section('body')
<header class="c-hd">
  <div class="wrap c-hd-in">
    <a href="{{ route('home') }}" class="lg" aria-label="{{ config('site.name') }} — Beranda">@include('partials.logo')</a>
    <nav class="c-nav" aria-label="Menu">
      <a href="{{ route('prices.index') }}" @class(['on' => request()->routeIs('prices.index')])>Cek Harga</a>
      <a href="{{ route('services.index') }}">Layanan</a>
      <a href="{{ route('about') }}">Klien</a>
      <a href="{{ route('articles.index') }}">Artikel</a>
      <a href="{{ route('home') }}">Beranda</a>
    </nav>
    <a class="c-st" href="{{ route('status') }}">Cek Status Servis</a>
  </div>
</header>

<main id="main">
  @yield('content')

  <section class="c-sec wrap">
    <h2 class="c-h">Layanan</h2>
    <div class="c-svc">
      @foreach (config('site.services') as $slug => $s)
        <a href="{{ route('services.show', $slug) }}">@include('partials.icon', ['name' => $s['icon']]){{ $s['title'] }}</a>
      @endforeach
    </div>
  </section>
</main>

@include('partials.footer', ['class' => 'c-ft'])
@endsection
