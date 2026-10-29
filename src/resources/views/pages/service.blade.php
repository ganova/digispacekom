@extends('layouts.company')

@section('title', $service['title'])
@section('description', $service['title'].' se-'.config('site.area').': '.$service['short'].' Teknisi datang ke lokasi, biaya disepakati dulu.')

@php($device = ['laptop' => 'Laptop', 'pc' => 'PC', 'macbook' => 'MacBook', 'printer' => 'Printer', 'jaringan' => 'Jaringan'][$service['price_category']] ?? null)

@section('content')
@include('partials.page-hero', [
    'crumb' => '<span>/</span><a href="'.route('services.index').'">Layanan</a>',
    'eyebrow' => 'Layanan',
    'title' => $service['title'],
    'lead' => $service['intro'],
])

<section class="sec">
  <div class="wrap detail">
    <div>
      <div class="sec-hd rv"><span class="eyebrow">Yang biasa kami tangani</span><h2>Masalah umum, solusi cepat.</h2></div>
      <ul class="checks">
        @foreach ($service['problems'] as $problem)<li class="rv" style="--d:{{ $loop->index }}"><span class="ok" aria-hidden="true">✓</span>{{ $problem }}</li>@endforeach
      </ul>

      <div class="sec-hd rv" style="margin-top:64px"><span class="eyebrow">Cara kerja</span><h2>Tiga langkah, tanpa ribet.</h2></div>
      <ol class="flow flow-col rv">
        @foreach (config('site.steps') as [$t, $d])<li style="--d:{{ $loop->index }}"><span class="flow-n mono">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><b>{{ $t }}</b><p>{{ $d }}</p></li>@endforeach
      </ol>
    </div>

    <aside class="aside rv">
      <span class="ic-box">@include('partials.icon', ['name' => $service['icon']])</span>
      <h2>Butuh {{ strtolower($service['title']) }}?</h2>
      <p>Ceritakan keluhannya, kami balas lewat WhatsApp dalam jam kerja.</p>
      <a class="btn btn-pri btn-block" href="{{ route('landing', 'servis-panggilan') }}?{{ http_build_query(array_filter(['perangkat' => $device])) }}#booking" data-magnetic>Booking teknisi<span class="arw" aria-hidden="true">→</span></a>
      @if ($service['price_category'])
        <a class="btn btn-ghost btn-block" href="{{ route('prices.index', ['kategori' => $service['price_category']]) }}">Lihat estimasi harga</a>
      @else
        <a class="btn btn-ghost btn-block" href="{{ route('contact') }}#penawaran">Minta penawaran</a>
      @endif
      <p class="aside-ft"><span class="dot-live" aria-hidden="true"></span>{{ config('site.hours') }}</p>
    </aside>
  </div>
</section>

<section class="sec sec-soft">
  <div class="wrap">
    <div class="sec-hd rv"><span class="eyebrow">Lainnya</span><h2>Layanan lainnya</h2></div>
    <div class="bento">
      @foreach ($others as $slug => $s)
        <a class="card spot rv" style="--d:{{ $loop->index }}" href="{{ route('services.show', $slug) }}"><span class="ic-box">@include('partials.icon', ['name' => $s['icon']])</span><b>{{ $s['title'] }}</b><span>{{ $s['short'] }}</span><i class="card-go" aria-hidden="true">→</i></a>
      @endforeach
    </div>
  </div>
</section>
@endsection
