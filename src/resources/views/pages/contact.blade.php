@extends('layouts.company')

@section('title', 'Kontak & Minta Penawaran')
@section('description', 'Hubungi '.config('site.name').' lewat WhatsApp '.config('site.phone').' atau kirim permintaan penawaran untuk kantor dan instansi.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Kontak',
    'title' => 'Hubungi kami',
    'lead' => 'Untuk service panggilan, chat WhatsApp paling cepat. Kantor dan instansi bisa mengirim permintaan penawaran di bawah ini.',
])

<section class="b-sec">
  <div class="wrap b-contact">
    <ul class="b-info">
      <li>@include('partials.icon', ['name' => 'phone'])<span><b>WhatsApp &amp; telepon</b><a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener">{{ config('site.phone') }}</a></span></li>
      <li>@include('partials.icon', ['name' => 'mail'])<span><b>Email</b><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></span></li>
      <li>@include('partials.icon', ['name' => 'clock'])<span><b>Jam operasional</b>{{ config('site.hours') }}</span></li>
      <li>@include('partials.icon', ['name' => 'pin'])<span><b>Kantor</b>{{ config('site.address') }} · melayani {{ config('site.area') }}</span></li>
    </ul>

    <form class="b-form" id="penawaran" method="post" action="{{ route('booking.store') }}">
      @csrf
      <input type="hidden" name="type" value="penawaran">
      <input type="hidden" name="source" value="kontak">
      <div class="hp" aria-hidden="true"><label>Website <input name="website" tabindex="-1" autocomplete="off"></label></div>
      <h2>Minta penawaran</h2>
      @include('partials.errors')
      <div class="b-row">
        <label>Nama <input name="name" value="{{ old('name') }}" required autocomplete="name"></label>
        <label>Instansi / perusahaan <input name="organization" value="{{ old('organization') }}" autocomplete="organization"></label>
      </div>
      <div class="b-row">
        <label>Layanan
          <select name="device">
            @foreach (config('site.services') as $s)<option @selected(old('device') === $s['title'])>{{ $s['title'] }}</option>@endforeach
            <option @selected(old('device') === 'Kontrak maintenance')>Kontrak maintenance</option>
          </select>
        </label>
        <label>Jumlah unit <input name="units" type="number" min="1" inputmode="numeric" value="{{ old('units') }}"></label>
      </div>
      <label>Kebutuhan <textarea name="complaint" rows="4" required placeholder="Contoh: maintenance 30 PC dan 5 printer, kunjungan 2x sebulan">{{ old('complaint') }}</textarea></label>
      <label>Lokasi <input name="location" value="{{ old('location') }}" placeholder="Contoh: Jakarta Pusat"></label>
      <button class="b-p" type="submit">Kirim lewat WhatsApp</button>
      <small>Data dikirim ke WhatsApp kami, tidak dibagikan ke pihak lain.</small>
    </form>
  </div>

  <div class="wrap">
    <iframe class="b-map" title="Lokasi {{ config('site.name') }}" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
      src="https://www.google.com/maps?q={{ urlencode(config('site.maps_query')) }}&output=embed"></iframe>
  </div>
</section>
@endsection
