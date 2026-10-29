{{-- Konsep B · Mitra IT Kantor — chrome untuk seluruh halaman company profile --}}
@extends('layouts.base')

@section('body_class', 'cB')
@section('hero_tone', 'dark')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/company.css') }}">
@endpush

@section('body')
@include('partials.header', [
    'links' => [
        ['Layanan', route('services.index'), request()->routeIs('services.*')],
        ['Service Panggilan', route('landing', 'servis-panggilan'), false],
        ['Tentang & Klien', route('about'), request()->routeIs('about')],
        ['Artikel', route('articles.index'), request()->routeIs('articles.*')],
        ['Kontak', route('contact'), request()->routeIs('contact')],
        ['Cek Harga', route('prices.index'), false],
    ],
    'cta' => ['Minta Penawaran', route('contact').'#penawaran'],
])

<main id="main">
  @yield('content')
</main>

@include('partials.footer')
@endsection
