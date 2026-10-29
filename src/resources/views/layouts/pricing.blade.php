{{-- Konsep C · Cek Harga & Status — menu terpisah untuk pengunjung yang ingin cek biaya --}}
@extends('layouts.base')

@section('body_class', 'cC')
@section('hero_tone', 'light')

@push('styles')
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/pricing.css') }}">
@endpush

@section('body')
@include('partials.header', [
    'links' => [
        ['Cek Harga', route('prices.index'), request()->routeIs('prices.index')],
        ['Cek Status', route('status'), request()->routeIs('status')],
        ['Layanan', route('services.index'), false],
        ['Klien', route('about'), false],
        ['Beranda', route('home'), false],
    ],
    'cta' => ['Booking Teknisi', route('landing', 'servis-panggilan').'#booking'],
])

<main id="main">
  @yield('content')

  <section class="c-sec wrap">
    <div class="sec-hd rv">
      <span class="eyebrow">Layanan</span>
      <h2>Semua urusan komputer, satu tim</h2>
    </div>
    <div class="c-svc">
      @foreach (config('site.services') as $slug => $s)
        <a class="spot rv" style="--d:{{ $loop->index }}" href="{{ route('services.show', $slug) }}">@include('partials.icon', ['name' => $s['icon']])<span>{{ $s['title'] }}</span><i class="arw" aria-hidden="true">→</i></a>
      @endforeach
    </div>
  </section>
</main>

@include('partials.footer')
@endsection
