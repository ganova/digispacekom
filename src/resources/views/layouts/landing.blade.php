{{-- Konsep A · Teknisi Datang — landing page iklan, navigasi minimal agar fokus ke booking --}}
@extends('layouts.base')

@section('fonts', 'family=Plus+Jakarta+Sans:wght@400;600;700;800')
@section('body_class', 'cA')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/landing.css') }}">
@endpush

@section('body')
<div class="a-top">Service panggilan <b>se-{{ config('site.area') }}</b> · {{ config('site.hours') }} · {{ config('site.phone') }}</div>
<header class="a-hd">
  <div class="wrap a-hd-in">
    <a href="{{ route('home') }}" class="lg" aria-label="{{ config('site.name') }} — Beranda">@include('partials.logo')</a>
    <nav class="a-nav" aria-label="Bagian halaman">
      <a href="#layanan">Layanan</a>
      <a href="{{ route('prices.index') }}">Harga</a>
      <a href="#klien">Klien</a>
      <a href="{{ route('articles.index') }}">Artikel</a>
      <a href="{{ route('contact') }}">Kontak</a>
    </nav>
    <a class="a-bk" href="#booking">Booking Teknisi</a>
  </div>
</header>

<main id="main">
  @yield('content')
</main>

@include('partials.footer', ['class' => 'a-ft'])
@endsection
