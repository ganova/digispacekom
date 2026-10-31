@extends('layouts.company')

@section('title', 'Kontak & Minta Penawaran')
@section('description', 'Hubungi '.config('site.name').' lewat WhatsApp '.config('site.phone').' atau kirim permintaan penawaran untuk kantor dan instansi.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Kontak',
    'titleParts' => [['Mari bicara soal'], ['IT kantor Anda.', 'grad']],
    'lead' => 'Untuk service panggilan, chat WhatsApp paling cepat. Kantor dan instansi bisa mengirim permintaan penawaran di bawah ini.',
])

<section class="sec">
  <div class="wrap contact">
    <div class="contact-l">
    <ul class="info">
      <li class="spot rv" style="--d:0"><span class="ic-box">@include('partials.icon', ['name' => 'phone'])</span><span><small>WhatsApp &amp; telepon</small><a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener">{{ config('site.phone') }}</a></span></li>
      <li class="spot rv" style="--d:1"><span class="ic-box">@include('partials.icon', ['name' => 'mail'])</span><span><small>Email</small><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></span></li>
      <li class="spot rv" style="--d:2"><span class="ic-box">@include('partials.icon', ['name' => 'clock'])</span><span><small>Jam operasional</small>{{ config('site.hours') }}</span></li>
      <li class="spot rv" style="--d:3"><span class="ic-box">@include('partials.icon', ['name' => 'pin'])</span><span><small>Kantor</small>{{ config('site.address') }} · melayani {{ config('site.area') }}</span></li>
    </ul>

    <section class="about-card spot rv" style="--d:4" aria-label="Tentang, visi, dan misi">
      <span class="eyebrow">Visi &amp; misi</span>
      <h3>Visi</h3>
      <p>{{ config('site.vision') }}</p>
      <h3>Misi</h3>
      <ul class="checks checks-1">
        @foreach (config('site.mission') as $m)<li><span class="ok" aria-hidden="true">✓</span>{{ $m }}</li>@endforeach
      </ul>
      <a class="link" href="{{ route('about') }}">Selengkapnya tentang kami →</a>
    </section>
    </div>

    <form class="qform rv" id="penawaran" method="post" action="{{ route('booking.store') }}">
      @csrf
      <input type="hidden" name="type" value="penawaran">
      <input type="hidden" name="source" value="kontak">
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <div>
        <span class="eyebrow">Untuk kantor &amp; instansi</span>
        <h2>Minta penawaran</h2>
      </div>
      @include('partials.errors')
      <div class="row2">
        <label class="field"><input name="name" value="{{ old('name') }}" required autocomplete="name" placeholder=" "><span>Nama</span></label>
        <label class="field"><input name="organization" value="{{ old('organization') }}" autocomplete="organization" placeholder=" "><span>Instansi / perusahaan</span></label>
      </div>
      <div class="row2">
        <label class="field">
          <select name="device">
            @foreach (config('site.services') as $s)<option @selected(old('device') === $s['title'])>{{ $s['title'] }}</option>@endforeach
            <option @selected(old('device') === 'Kontrak maintenance')>Kontrak maintenance</option>
          </select><span>Layanan</span>
        </label>
        <label class="field"><input name="units" type="number" min="1" inputmode="numeric" value="{{ old('units') }}" placeholder=" "><span>Jumlah unit</span></label>
      </div>
      <label class="field"><textarea name="complaint" rows="4" required placeholder="Contoh: maintenance 30 PC dan 5 printer, kunjungan 2x sebulan">{{ old('complaint') }}</textarea><span>Kebutuhan</span></label>
      <label class="field"><input name="location" value="{{ old('location') }}" placeholder="Contoh: Jakarta Pusat"><span>Lokasi</span></label>
      <button class="btn btn-pri btn-block" type="submit" data-magnetic>Kirim lewat WhatsApp<span class="arw" aria-hidden="true">→</span></button>
      <small class="qform-note">Data dikirim ke WhatsApp kami, tidak dibagikan ke pihak lain.</small>
    </form>
  </div>

  <div class="wrap">
    <iframe class="map rv" title="Lokasi {{ config('site.name') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
      src="https://www.google.com/maps?q={{ urlencode(config('site.maps_query')) }}&output=embed"></iframe>
  </div>
</section>
@endsection
