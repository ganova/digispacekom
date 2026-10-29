{{-- Konsep B · Mitra IT Kantor — chrome untuk seluruh halaman company profile --}}
@extends('layouts.base')

@section('fonts', 'family=IBM+Plex+Sans:wght@400;500;600;700')
@section('body_class', 'cB')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/company.css') }}">
@endpush

@section('body')
<header class="b-hd">
  <div class="wrap b-hd-in">
    <a href="{{ route('home') }}" class="lg" aria-label="{{ config('site.name') }} — Beranda">@include('partials.logo')</a>
    <button class="nav-tg" type="button" aria-expanded="false" aria-controls="b-nav"><span></span><span class="sr-only">Menu</span></button>
    <nav id="b-nav" class="b-nav" aria-label="Menu utama">
      <a href="{{ route('services.index') }}" @class(['on' => request()->routeIs('services.*')])>Layanan</a>
      <a href="{{ route('landing', 'servis-panggilan') }}">Service Panggilan</a>
      <a href="{{ route('about') }}" @class(['on' => request()->routeIs('about')])>Tentang &amp; Klien</a>
      <a href="{{ route('articles.index') }}" @class(['on' => request()->routeIs('articles.*')])>Artikel</a>
      <a href="{{ route('contact') }}" @class(['on' => request()->routeIs('contact')])>Kontak</a>
      <a href="{{ route('prices.index') }}" class="b-nav-c">Cek Harga</a>
    </nav>
    <a class="b-q" href="{{ route('contact') }}#penawaran">Minta Penawaran</a>
  </div>
</header>

<main id="main">
  @yield('content')
</main>

<section class="b-band">
  <div class="wrap b-band-in">
    <b>Perangkat kantor bermasalah hari ini?</b>
    <a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener">Hubungi {{ config('site.phone') }}</a>
  </div>
</section>

@include('partials.footer', ['class' => 'b-ft'])
@endsection
