@extends('layouts.landing')

@section('title', $page['meta_title'])
@section('description', $page['meta_description'])

@php($selected = old('device', $device && in_array($device, $page['devices'], true) ? $device : $page['devices'][0]))

@section('content')
<section class="a-hero">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap a-hero-in">
    <div class="a-copy">
      <span class="eyebrow eyebrow-inv fade-up" style="--fd:.05s">Service panggilan · {{ config('site.area') }}</span>
      <h1 class="split">{!! \App\Support\Site::split([[$page['headline']], [$page['highlight'], 'grad']]) !!}</h1>
      <p class="lead fade-up" style="--fd:.5s">{{ $page['lead'] }}</p>
      <ul class="a-chk fade-up" style="--fd:.65s">@foreach ($page['checks'] as $check)<li><span class="ok" aria-hidden="true">✓</span>{{ $check }}</li>@endforeach</ul>
      <div class="a-proof fade-up" style="--fd:.8s">
        <div class="a-av" aria-hidden="true"><i>DS</i><i>IT</i><i>✓</i></div>
        <p>Dipercaya <b>kedutaan, KPU, sekolah</b>, dan kantor swasta di Jakarta.</p>
      </div>
    </div>

    <form class="a-form fade-up" style="--fd:.3s" id="booking" method="post" action="{{ route('booking.store') }}">
      @csrf
      <input type="hidden" name="source" value="{{ $slug }}">
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <div class="a-form-hd">
        <h2>{{ $page['form_title'] }}</h2>
        <span class="a-badge"><span class="dot-live" aria-hidden="true"></span>Balas cepat via WA</span>
      </div>
      @include('partials.errors')
      <fieldset>
        <legend>Perangkat</legend>
        <div class="a-chips">
          @foreach ($page['devices'] as $d)
            <label><input type="radio" name="device" value="{{ $d }}" @checked($selected === $d)><span>{{ $d }}</span></label>
          @endforeach
        </div>
      </fieldset>
      <label class="field"><textarea name="complaint" rows="3" required placeholder="{{ $page['placeholder'] }}">{{ old('complaint', $complaint) }}</textarea><span>Keluhan</span></label>
      <div class="row2">
        <label class="field"><input name="location" value="{{ old('location') }}" required placeholder="Contoh: Bekasi Barat" autocomplete="address-level2"><span>Lokasi</span></label>
        <label class="field"><input name="name" value="{{ old('name') }}" autocomplete="name" placeholder=" "><span>Nama <span class="opt">(opsional)</span></span></label>
      </div>
      <button class="btn btn-lime btn-block a-send" type="submit">@include('partials.icon', ['name' => 'wa'])Kirim lewat WhatsApp</button>
      <p class="a-safe">🔒 Tanpa biaya booking. Biaya servis disepakati setelah dicek.</p>
    </form>
  </div>

  <div class="a-clients" id="klien">
    <div class="marquee">
      @foreach ([1, 2] as $copy)
        <div @if ($copy === 2) aria-hidden="true" @endif>@foreach (\App\Support\Site::clients() as $client)@if ($client['logo_url'])<i class="cl-chip" title="{{ $client['name'] }}"><img src="{{ $client['logo_url'] }}" alt="{{ $client['name'] }}" loading="lazy" decoding="async"></i>@else<span>{{ $client['name'] }}</span>@endif @endforeach</div>
      @endforeach
    </div>
  </div>
</section>

<section class="sec" id="layanan">
  <div class="wrap">
    <div class="sec-hd rv">
      <span class="eyebrow">Layanan kami</span>
      <h2>Satu tim untuk semua urusan <span class="grad-dark">komputer.</span></h2>
      <p class="lead">Rumah maupun kantor, teknisi datang dengan peralatan lengkap.</p>
    </div>
    <div class="bento">
      @foreach (config('site.services') as $key => $s)
        <a class="card spot rv" style="--d:{{ $loop->index }}" href="{{ route('services.show', $key) }}"><span class="ic-box">@include('partials.icon', ['name' => $s['icon']])</span><b>{{ $s['title'] }}</b><span>{{ $s['short'] }}</span><i class="card-go" aria-hidden="true">→</i></a>
      @endforeach
    </div>
  </div>
</section>

<section class="sec sec-dark" id="cara-kerja">
  <div class="aurora aurora-soft" aria-hidden="true"><i></i><i></i></div>
  <div class="wrap">
    <div class="sec-hd rv"><span class="eyebrow eyebrow-inv">Cara kerja</span><h2>Booking sekarang, teknisi datang.</h2></div>
    <ol class="flow flow-dark rv">
      @foreach (config('site.steps') as [$t, $d])
        <li style="--d:{{ $loop->index }}"><span class="flow-n mono">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><b>{{ $t }}</b><p>{{ $d }}</p></li>
      @endforeach
    </ol>
  </div>
</section>

<section class="sec">
  <div class="wrap">
    <div class="a-final spot rv">
      <h2>Jangan biarkan perangkat bermasalah menunda pekerjaan.</h2>
      <p class="lead">Isi form singkat, teknisi kami menghubungi lewat WhatsApp.</p>
      <div class="hero-cta">
        <a class="btn btn-pri" href="#booking" data-magnetic>Booking teknisi sekarang<span class="arw" aria-hidden="true">→</span></a>
        <a class="btn btn-ghost" href="{{ route('prices.index') }}">Cek estimasi harga</a>
      </div>
    </div>
  </div>
</section>
@endsection
