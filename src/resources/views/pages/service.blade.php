@extends('layouts.company')

@section('title', $service['title'])
@section('description', $service['title'].' se-'.config('site.area').': '.$service['short'].' Teknisi datang ke lokasi, biaya disepakati dulu.')

@php($device = ['laptop' => 'Laptop', 'pc' => 'PC', 'macbook' => 'MacBook', 'printer' => 'Printer', 'jaringan' => 'Jaringan'][$service['price_category']] ?? null)

@section('content')
@include('partials.page-hero', [
    'crumb' => ' / <a href="'.route('services.index').'">Layanan</a>',
    'eyebrow' => 'Layanan',
    'title' => $service['title'],
    'lead' => $service['intro'],
])

<section class="b-sec">
  <div class="wrap b-detail">
    <div>
      <h2>Yang biasa kami tangani</h2>
      <ul class="b-checks">
        @foreach ($service['problems'] as $problem)<li>{{ $problem }}</li>@endforeach
      </ul>

      <h2>Cara kerjanya</h2>
      <ol class="b-steps">
        @foreach (config('site.steps') as [$t, $d])<li><b>{{ $t }}</b>{{ $d }}</li>@endforeach
      </ol>
    </div>

    <aside class="b-card b-aside">
      <h2>Butuh {{ strtolower($service['title']) }}?</h2>
      <p>Ceritakan keluhannya, kami balas lewat WhatsApp.</p>
      <a class="b-p" href="{{ route('landing', 'servis-panggilan') }}?{{ http_build_query(array_filter(['perangkat' => $device])) }}#booking">Booking teknisi</a>
      @if ($service['price_category'])
        <a class="b-s" href="{{ route('prices.index', ['kategori' => $service['price_category']]) }}">Lihat estimasi harga</a>
      @else
        <a class="b-s" href="{{ route('contact') }}#penawaran">Minta penawaran</a>
      @endif
    </aside>
  </div>
</section>

<section class="b-sec b-sec-alt">
  <div class="wrap">
    <h2>Layanan lainnya</h2>
    <div class="b-cards">
      @foreach ($others as $slug => $s)
        <a class="b-svc" href="{{ route('services.show', $slug) }}">@include('partials.icon', ['name' => $s['icon']])<b>{{ $s['title'] }}</b><span>{{ $s['short'] }}</span></a>
      @endforeach
    </div>
  </div>
</section>
@endsection
