@extends('layouts.pricing')

@section('title', 'Cek Harga Service Laptop, PC & Printer')
@section('description', 'Cari keluhan perangkat dan lihat estimasi biaya service laptop, PC, MacBook, printer, dan jaringan sebelum teknisi datang.')
@section('wa_label', 'Tanya harga lewat WA')

@php
    $categories = \App\Models\PriceItem::CATEGORIES;
    $tagUrl = fn ($key) => route('prices.index', array_filter(['q' => $q, 'kategori' => $key]));
@endphp

@section('content')
<section class="c-hero">
  <div class="c-blob" aria-hidden="true"><i></i><i></i></div>
  <div class="wrap c-hero-in">
    <div class="c-copy">
      <span class="eyebrow fade-up" style="--fd:.05s">Cek harga · transparan</span>
      <h1 class="split">{!! \App\Support\Site::split([['Tahu kisaran biayanya'], ['sebelum', 'hl'], ['teknisi datang.']]) !!}</h1>
      <p class="lead fade-up" style="--fd:.5s">Cari keluhan perangkatmu, lihat estimasi biaya, lalu booking teknisi panggilan se-{{ config('site.area') }}.</p>
      <ul class="c-trust fade-up" style="--fd:.65s">
        <li><span class="ok" aria-hidden="true">✓</span>Harga final disepakati sebelum dikerjakan</li>
        <li><span class="ok" aria-hidden="true">✓</span>Tanpa biaya booking</li>
      </ul>
    </div>

    <div class="c-box fade-up" style="--fd:.3s">
      <form class="c-srch" method="get" action="{{ route('prices.index') }}" role="search" data-live-search="c-list">
        <input type="hidden" name="kategori" value="{{ $category }}">
        <label for="q">@include('partials.icon', ['name' => 'search'])<span class="sr-only">Cari keluhan</span></label>
        <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Coba: laptop mati, printer macet" autocomplete="off">
        <button type="submit" class="btn btn-pri btn-sm">Cari</button>
      </form>

      <nav class="c-tags" aria-label="Kategori">
        <span class="seg-ind" aria-hidden="true"></span>
        <a href="{{ $tagUrl(null) }}" data-cat="" @class(['on' => ! $category])>Semua</a>
        @foreach ($categories as $key => $label)
          <a href="{{ $tagUrl($key) }}" data-cat="{{ $key }}" @class(['on' => $category === $key])>{{ $label }}</a>
        @endforeach
      </nav>

      <div class="c-list" id="c-list" aria-live="polite">
        @include('prices._list')
      </div>
      <p class="c-note">Estimasi awal. Harga final disepakati setelah teknisi mengecek perangkat.</p>
    </div>
  </div>
</section>

<section class="c-sec wrap">
  @include('prices._tracker', ['code' => '', 'ticket' => null, 'compact' => true])
</section>
@endsection
