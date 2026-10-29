@extends('layouts.landing')

@section('title', $page['meta_title'])
@section('description', $page['meta_description'])

@php($selected = old('device', $device && in_array($device, $page['devices'], true) ? $device : $page['devices'][0]))

@section('content')
<section class="a-hero">
  <div class="wrap a-hero-in">
    <div>
      <h1>{{ $page['headline'] }} <span>{{ $page['highlight'] }}</span></h1>
      <p>{{ $page['lead'] }}</p>
      <ul class="a-chk">@foreach ($page['checks'] as $check)<li>{{ $check }}</li>@endforeach</ul>
    </div>

    <form class="a-form" id="booking" method="post" action="{{ route('booking.store') }}">
      @csrf
      <input type="hidden" name="source" value="{{ $slug }}">
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <h2>{{ $page['form_title'] }}</h2>
      @include('partials.errors')
      <fieldset>
        <legend>Perangkat</legend>
        <div class="a-chips">
          @foreach ($page['devices'] as $d)
            <label><input type="radio" name="device" value="{{ $d }}" @checked($selected === $d)><span>{{ $d }}</span></label>
          @endforeach
        </div>
      </fieldset>
      <label>Keluhan <textarea name="complaint" rows="2" required placeholder="{{ $page['placeholder'] }}">{{ old('complaint', $complaint) }}</textarea></label>
      <label>Lokasi <input name="location" value="{{ old('location') }}" required placeholder="Contoh: Bekasi Barat" autocomplete="address-level2"></label>
      <label>Nama <span class="opt">(opsional)</span><input name="name" value="{{ old('name') }}" autocomplete="name"></label>
      <button class="a-send" type="submit">Kirim lewat WhatsApp</button>
    </form>
  </div>
</section>

<section class="a-sec" id="layanan">
  <div class="wrap">
    <h2>Layanan kami</h2>
    <p class="a-sub">Satu tim untuk semua urusan komputer rumah dan kantor.</p>
    <div class="a-svc">
      @foreach (config('site.services') as $key => $s)
        <a href="{{ route('services.show', $key) }}">@include('partials.icon', ['name' => $s['icon']])<b>{{ $s['title'] }}</b><small>{{ $s['short'] }}</small></a>
      @endforeach
    </div>
  </div>
</section>

<section class="a-sec a-sec-tight">
  <div class="wrap">
    <div class="a-steps">
      @foreach (config('site.steps') as [$t, $d])
        <div><i>{{ $loop->iteration }}</i><span><b>{{ $t }}</b>{{ $d }}</span></div>
      @endforeach
    </div>
  </div>
</section>

<section class="a-sec a-sec-tight a-center" id="klien">
  <div class="wrap">
    <p class="a-sub">Dipercaya kantor &amp; instansi di Jakarta</p>
    <div class="a-cl">@foreach (config('site.clients') as $client)<span>{{ $client }}</span>@endforeach</div>
    <a class="a-bk a-bk-lg" href="#booking">Booking teknisi sekarang</a>
  </div>
</section>
@endsection
