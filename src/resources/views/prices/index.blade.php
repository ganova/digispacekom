@extends('layouts.pricing')

@section('title', 'Cek Harga Service Laptop, PC & Printer')
@section('description', 'Cari keluhan perangkat dan lihat estimasi biaya service laptop, PC, MacBook, printer, dan jaringan sebelum teknisi datang.')
@section('wa_label', 'Tanya harga lewat WA')

@php
    $categories = \App\Models\PriceItem::CATEGORIES;
    $tagUrl = fn ($key) => route('prices.index', array_filter(['q' => $q, 'kategori' => $key]));
@endphp

@section('content')
<section class="c-hero wrap">
  <div>
    <h1>Tahu kisaran biayanya <em>sebelum</em> teknisi datang.</h1>
    <p>Cari keluhan perangkatmu, lihat estimasi biaya, lalu booking teknisi panggilan se-{{ config('site.area') }}.</p>
  </div>

  <div class="c-box">
    <form class="c-srch" method="get" action="{{ route('prices.index') }}" role="search">
      @if ($category)<input type="hidden" name="kategori" value="{{ $category }}">@endif
      <label for="q">@include('partials.icon', ['name' => 'search'])<span class="sr-only">Cari keluhan</span></label>
      <input id="q" name="q" type="search" value="{{ $q }}" placeholder="Contoh: laptop mati, printer macet" autocomplete="off">
      <button type="submit">Cari</button>
    </form>

    <nav class="c-tags" aria-label="Kategori">
      <a href="{{ $tagUrl(null) }}" @class(['on' => ! $category])>Semua</a>
      @foreach ($categories as $key => $label)
        <a href="{{ $tagUrl($key) }}" @class(['on' => $category === $key])>{{ $label }}</a>
      @endforeach
    </nav>

    <div class="c-list" aria-live="polite">
      @forelse ($items as $item)
        <div class="c-row">
          <b>{{ $item->name }}</b>
          <strong>{{ $item->priceLabel() }}</strong>
          <small>{{ $item->note }}</small>
          <a href="{{ route('landing', 'servis-panggilan') }}?{{ http_build_query(['perangkat' => $categories[$item->category], 'keluhan' => $item->name]) }}#booking">Booking</a>
        </div>
      @empty
        <p class="c-empty">Belum ada estimasi untuk "{{ $q }}". <a href="{{ \App\Support\Site::whatsappUrl('Halo, saya mau tanya biaya service: '.$q) }}" target="_blank" rel="noopener">Tanya langsung lewat WhatsApp</a>.</p>
      @endforelse
    </div>
    <p class="c-note">Estimasi awal. Harga final disepakati setelah teknisi mengecek perangkat.</p>
  </div>
</section>

<section class="c-sec wrap">
  @include('prices._tracker', ['code' => '', 'ticket' => null, 'compact' => true])
</section>
@endsection
