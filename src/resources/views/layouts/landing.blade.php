{{-- Konsep A · Teknisi Datang — landing page iklan, navigasi minimal agar fokus ke booking --}}
@extends('layouts.base')

@section('body_class', 'cA')
@section('hero_tone', 'dark')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/landing.css') }}">
@endpush

@section('body')
<div class="a-top"><span class="dot-live" aria-hidden="true"></span>Service panggilan <b>se-{{ config('site.area') }}</b><span class="a-top-x"> · {{ config('site.hours') }} · {{ config('site.phone') }}</span></div>
@include('partials.header', [
    'links' => [
        ['Layanan', '#layanan', false],
        ['Cara kerja', '#cara-kerja', false],
        ['Harga', route('prices.index'), false],
        ['Klien', '#klien', false],
        ['Kontak', route('contact'), false],
    ],
    'cta' => ['Booking Teknisi', '#booking'],
])

<main id="main">
  @yield('content')
</main>

@include('partials.footer')

<div class="a-sticky" aria-hidden="true">
  <a class="btn btn-lime" href="#booking" tabindex="-1">Booking teknisi</a>
  <a class="btn btn-ghost-inv" href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener" tabindex="-1">WhatsApp</a>
</div>
@endsection
