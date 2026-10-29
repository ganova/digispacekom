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
{{-- HERO --}}
<section class="hero hero-fx">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap hero-in">
    <div class="hero-copy">
      <a class="chip fade-up" style="--fd:.05s" href="{{ route('landing', 'it-support-kantor') }}"><span class="dot-live" aria-hidden="true"></span>IT support · {{ config('site.area') }}<span class="chip-go">Kontrak maintenance →</span></a>
      <h1 class="split">
        {!! \App\Support\Site::split([['Mitra IT untuk']]) !!}
        <span class="rot" aria-label="kantor, sekolah, dan instansi">
          <span class="rot-in" aria-hidden="true"><span>kantor.</span><span>sekolah.</span><span>instansi.</span><span>rumah.</span><span>kantor.</span></span>
        </span>
      </h1>
      <p class="lead fade-up" style="--fd:.5s">Perawatan rutin, perbaikan, jaringan, sampai pengadaan perangkat, ditangani satu tim. Layanan panggilan untuk rumah juga tersedia.</p>
      <div class="hero-cta fade-up" style="--fd:.65s">
        <a class="btn btn-lime" href="{{ route('contact') }}#penawaran" data-magnetic>Minta Penawaran<span class="arw" aria-hidden="true">→</span></a>
        <a class="btn btn-ghost-inv" href="{{ route('landing', 'servis-panggilan') }}">Service panggilan</a>
      </div>
      <dl class="hero-stats fade-up" style="--fd:.8s">
        <div><dt>Layanan</dt><dd><span data-count="{{ count(config('site.services')) }}">{{ count(config('site.services')) }}</span> bidang</dd></div>
        <div><dt>Garansi servis</dt><dd><span data-count="30">30</span> hari</dd></div>
        <div><dt>Jam layanan</dt><dd>08–20</dd></div>
      </dl>
    </div>

    {{-- Ilustrasi: contoh laporan kontrak maintenance --}}
    <div class="hero-vis fade-up" style="--fd:.35s">
      <div class="dash" data-tilt>
        <div class="dash-hd">
          <span class="dash-dots" aria-hidden="true"><i></i><i></i><i></i></span>
          <b>Kontrak maintenance kantor</b>
          <span class="dash-tag">Contoh laporan</span>
        </div>
        <div class="dash-body">
          <div class="dash-kpi">
            <div><small>Kesehatan aset IT</small><strong class="mono">96<em>%</em></strong></div>
            <svg class="spark" viewBox="0 0 120 40" aria-hidden="true"><path d="M0 30 L15 26 L30 28 L45 18 L60 20 L75 12 L90 14 L105 6 L120 8"/><path class="spark-fill" d="M0 30 L15 26 L30 28 L45 18 L60 20 L75 12 L90 14 L105 6 L120 8 V40 H0Z"/></svg>
          </div>
          <ul class="dash-bars">
            <li><span>Laptop &amp; PC</span><i style="--v:.97"></i><b class="mono">97%</b></li>
            <li><span>Printer</span><i style="--v:.92"></i><b class="mono">92%</b></li>
            <li><span>Jaringan</span><i style="--v:.99"></i><b class="mono">99%</b></li>
            <li><span>Server &amp; CCTV</span><i style="--v:.95"></i><b class="mono">95%</b></li>
          </ul>
          <ul class="dash-list">
            <li><span class="ok">✓</span>Kunjungan rutin &amp; cek kesehatan perangkat</li>
            <li><span class="ok">✓</span>Prioritas perbaikan saat ada kendala</li>
            <li><span class="ok">✓</span>Laporan kondisi aset IT tiap bulan</li>
          </ul>
        </div>
      </div>
      <div class="float f1"><span class="dot-live" aria-hidden="true"></span><span><b>Teknisi menuju lokasi</b><small>Contoh notifikasi</small></span></div>
      <div class="float f2">@include('partials.icon', ['name' => 'shield'])<span><b>Biaya disepakati dulu</b><small>Penawaran tertulis</small></span></div>
    </div>
  </div>

  <div class="hero-clients">
    <div class="wrap"><small>Dipercaya kantor &amp; instansi</small></div>
    <div class="marquee">
      @foreach ([1, 2] as $copy)
        <div @if ($copy === 2) aria-hidden="true" @endif>@foreach (config('site.clients') as $client)<span>{{ $client }}</span>@endforeach</div>
      @endforeach
    </div>
  </div>
</section>

{{-- LAYANAN PER SEGMEN --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-split">
      <div class="sec-hd rv">
        <span class="eyebrow">Layanan</span>
        <h2>Satu tim untuk <span class="grad-dark">semua urusan IT.</span></h2>
        <p class="lead">Dari laptop staf sampai jaringan gedung. Pilih kebutuhan Anda.</p>
      </div>
      <div class="seg rv" role="tablist" aria-label="Segmen pelanggan">
        <span class="seg-ind" aria-hidden="true"></span>
        @foreach ($segments as $key => $segment)
          <button type="button" role="tab" id="tab-{{ $key }}" aria-controls="seg-{{ $key }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? 0 : -1 }}" @class(['on' => $loop->first])>{{ $segment['label'] }}</button>
        @endforeach
      </div>
    </div>

    @foreach ($segments as $key => $segment)
      <div class="bento" role="tabpanel" id="seg-{{ $key }}" aria-labelledby="tab-{{ $key }}" @unless ($loop->first) hidden @endunless>
        @foreach ($segment['services'] as $slug)
          @php($s = config("site.services.$slug"))
          <a class="card spot rv" style="--d:{{ $loop->index }}" href="{{ route('services.show', $slug) }}">
            <span class="ic-box">@include('partials.icon', ['name' => $s['icon']])</span>
            <b>{{ $s['title'] }}</b>
            <span>{{ $s['short'] }}</span>
            <i class="card-go" aria-hidden="true">→</i>
          </a>
        @endforeach
      </div>
    @endforeach
  </div>
</section>

{{-- KENAPA KAMI --}}
<section class="sec sec-dark">
  <div class="aurora aurora-soft" aria-hidden="true"><i></i><i></i></div>
  <div class="wrap">
    <div class="sec-hd rv">
      <span class="eyebrow eyebrow-inv">Kenapa kami</span>
      <h2>Satu kontak untuk semua urusan IT kantor.</h2>
    </div>
    <div class="why">
      <div class="why-card rv" style="--d:0">
        <span class="why-n mono">01</span>
        @include('partials.icon', ['name' => 'shield'])
        <h3>Biaya jelas di awal</h3>
        <p>Penawaran tertulis sebelum pekerjaan dimulai, termasuk dokumen resmi untuk instansi.</p>
      </div>
      <div class="why-card rv" style="--d:1">
        <span class="why-n mono">02</span>
        @include('partials.icon', ['name' => 'clock'])
        <h3>Teknisi datang ke lokasi</h3>
        <p>Seluruh {{ config('site.area') }}, {{ config('site.hours') }}. Kerusakan ringan selesai di tempat.</p>
      </div>
      <div class="why-card rv" style="--d:2">
        <span class="why-n mono">03</span>
        @include('partials.icon', ['name' => 'server'])
        <h3>Paham perangkat kantor</h3>
        <p>Laptop, printer, server, jaringan, dan CCTV ditangani satu tim yang sama.</p>
      </div>
    </div>
  </div>
</section>

{{-- CARA KERJA --}}
<section class="sec">
  <div class="wrap">
    <div class="sec-hd rv">
      <span class="eyebrow">Cara kerja</span>
      <h2>Tiga langkah, tanpa ribet.</h2>
    </div>
    <ol class="flow rv">
      @foreach (config('site.steps') as [$t, $d])
        <li style="--d:{{ $loop->index }}"><span class="flow-n mono">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><b>{{ $t }}</b><p>{{ $d }}</p></li>
      @endforeach
    </ol>
  </div>
</section>

{{-- TEASER CEK HARGA (Konsep C) --}}
<section class="sec sec-tight">
  <div class="wrap">
    <div class="teaser spot rv">
      <div>
        <span class="eyebrow">Untuk pelanggan rumahan</span>
        <h2>Mau tahu kisaran biayanya dulu?</h2>
        <p class="lead">Cari keluhan perangkat dan lihat estimasi biaya sebelum memanggil teknisi. Perangkat yang sedang diservis bisa dicek statusnya dengan nomor tiket.</p>
        <div class="hero-cta">
          <a class="btn btn-pri" href="{{ route('prices.index') }}" data-magnetic>Cek Harga<span class="arw" aria-hidden="true">→</span></a>
          <a class="btn btn-ghost" href="{{ route('status') }}">Cek status servis</a>
        </div>
      </div>
      <div class="teaser-vis" aria-hidden="true">
        <div class="tv-search">@include('partials.icon', ['name' => 'search'])<span class="tv-type">laptop mati total</span></div>
        <div class="tv-row"><span>Laptop mati total</span><b class="mono">Rp150rb–450rb</b></div>
        <div class="tv-row"><span>Ganti SSD + pindah data</span><b class="mono">Rp350rb + SSD</b></div>
        <div class="tv-row"><span>Printer paper jam</span><b class="mono">Rp100rb–250rb</b></div>
      </div>
    </div>
  </div>
</section>

@if ($articles->isNotEmpty())
<section class="sec">
  <div class="wrap">
    <div class="sec-split">
      <div class="sec-hd rv"><span class="eyebrow">Artikel</span><h2>Catatan dari teknisi.</h2></div>
      <a class="link rv" href="{{ route('articles.index') }}">Semua artikel →</a>
    </div>
    @include('articles._grid', ['articles' => $articles])
  </div>
</section>
@endif
@endsection
