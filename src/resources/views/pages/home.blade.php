@extends('layouts.company')

@push('meta')
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'ComputerStore',
    'name' => config('site.name'),
    'url' => url('/'),
    'telephone' => config('site.phone'),
    'email' => config('site.email'),
    'address' => ['@type' => 'PostalAddress', 'addressLocality' => config('site.city'), 'addressCountry' => 'ID'],
    'areaServed' => config('site.service_areas'),
    'openingHours' => 'Mo-Sa 08:00-20:00',
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<section class="b-hero">
  <div class="wrap b-hero-in">
    <div>
      <span class="b-ey">IT support · {{ config('site.area') }}</span>
      <h1>Mitra IT untuk kantor, sekolah, dan instansi.</h1>
      <p>Perawatan rutin, perbaikan, jaringan, sampai pengadaan perangkat, ditangani satu tim. Layanan panggilan untuk rumah juga tersedia.</p>
      <div class="b-cta">
        <a class="b-p" href="{{ route('contact') }}#penawaran">Minta Penawaran</a>
        <a class="b-s" href="{{ route('landing', 'servis-panggilan') }}">Service panggilan</a>
      </div>
    </div>
    <div class="b-card">
      <h2>Kontrak maintenance kantor</h2>
      <ul>
        <li>Kunjungan rutin &amp; cek kesehatan perangkat</li>
        <li>Prioritas perbaikan saat ada kendala</li>
        <li>Perawatan jaringan, printer, dan server</li>
        <li>Laporan kondisi aset IT tiap bulan</li>
      </ul>
      <div class="pr"><span>Harga sesuai jumlah unit</span><a href="{{ route('landing', 'it-support-kantor') }}"><b>Konsultasi</b></a></div>
    </div>
  </div>
</section>

<section class="b-logos" id="klien">
  <div class="wrap b-logos-in">
    <small>Klien kami</small>
    <div>@foreach (config('site.clients') as $client)<span>{{ $client }}</span>@endforeach</div>
  </div>
</section>

<section class="b-sec">
  <div class="wrap">
    <h2>Layanan</h2>
    <div class="b-seg" role="tablist" aria-label="Segmen pelanggan">
      @foreach ($segments as $key => $segment)
        <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="seg-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @class(['on' => $loop->first])>{{ $segment['label'] }}</button>
      @endforeach
    </div>
    @foreach ($segments as $key => $segment)
      <div class="b-list" role="tabpanel" id="seg-{{ $key }}" aria-labelledby="tab-{{ $key }}" @unless ($loop->first) hidden @endunless>
        @foreach ($segment['services'] as $slug)
          @php($s = config("site.services.$slug"))
          <a href="{{ route('services.show', $slug) }}">@include('partials.icon', ['name' => $s['icon']])<span><b>{{ $s['title'] }}</b><small>{{ $s['short'] }}</small></span></a>
        @endforeach
      </div>
    @endforeach
  </div>
</section>

<section class="b-sec b-sec-alt">
  <div class="wrap b-why">
    <div>
      <h2>Kenapa kantor memilih kami</h2>
      <p class="b-lead">Satu kontak untuk semua urusan IT, dari komputer staf sampai jaringan gedung.</p>
    </div>
    <ul>
      <li>@include('partials.icon', ['name' => 'shield'])<span><b>Biaya jelas di awal</b>Penawaran tertulis sebelum pekerjaan dimulai, termasuk untuk instansi.</span></li>
      <li>@include('partials.icon', ['name' => 'clock'])<span><b>Teknisi datang ke lokasi</b>Seluruh {{ config('site.area') }}, {{ config('site.hours') }}.</span></li>
      <li>@include('partials.icon', ['name' => 'server'])<span><b>Paham perangkat kantor</b>Laptop, printer, server, jaringan, dan CCTV ditangani satu tim.</span></li>
    </ul>
  </div>
</section>

<section class="b-sec">
  <div class="wrap b-teaser">
    <div>
      <span class="b-ey">Untuk pelanggan rumahan</span>
      <h2>Mau tahu kisaran biayanya dulu?</h2>
      <p>Cari keluhan perangkat dan lihat estimasi biaya sebelum memanggil teknisi. Perangkat yang sedang diservis bisa dicek statusnya dengan nomor tiket.</p>
    </div>
    <div class="b-cta">
      <a class="b-p" href="{{ route('prices.index') }}">Cek Harga</a>
      <a class="b-s" href="{{ route('status') }}">Cek status servis</a>
    </div>
  </div>
</section>

@if ($articles->isNotEmpty())
<section class="b-sec b-sec-alt">
  <div class="wrap">
    <div class="b-sec-hd"><h2>Artikel</h2><a href="{{ route('articles.index') }}">Semua artikel</a></div>
    @include('articles._grid', ['articles' => $articles])
  </div>
</section>
@endif
@endsection
