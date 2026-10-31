@extends('admin.layout')

@section('title', 'Tab beranda')
@section('heading', 'Tab beranda')

@php
    // setelah gagal validasi, tampilkan isian terakhir pengguna
    $rows = old('segments')
        ? collect(old('segments'))->map(fn ($r) => ['key' => $r['key'] ?? null, 'label' => $r['label'] ?? '', 'services' => $r['services'] ?? []])->values()->all()
        : collect($segments)->map(fn ($s, $k) => ['key' => $k, 'label' => $s['label'], 'services' => $s['services']])->values()->all();
@endphp

@section('content')
<p class="note" style="margin-bottom:16px"><span>Tab di beranda memilih layanan yang tampil untuk tiap jenis pelanggan (mis. Kantor, Rumah, <strong>CCTV</strong>, <strong>Game Station</strong>). Tambah tab, beri nama, lalu centang layanannya. Layanan baru dibuat di menu <a href="{{ route('admin.services.index') }}">Layanan</a>.</span></p>

<form method="post" action="{{ route('admin.segments.update') }}" class="card">
  @csrf @method('put')
  <div class="card-head"><div><h2>Tab di beranda</h2><p class="sub">Urutan di sini adalah urutan tab. Tab pertama terbuka lebih dulu. Maksimal 8 tab.</p></div></div>

  <div class="events" id="segments">
    @foreach ($rows as $i => $row)
      <div class="ev seg-row" data-state="next">
        <span class="n">{{ $i + 1 }}</span>
        <div class="seg-body">
          <input type="hidden" name="segments[{{ $i }}][key]" value="{{ $row['key'] }}">
          <input class="seg-name" name="segments[{{ $i }}][label]" value="{{ $row['label'] }}" placeholder="Nama tab, mis. CCTV" maxlength="40" aria-label="Nama tab" required>
          <div class="sel-list">
            @foreach ($services as $slug => $service)
              <label><input type="checkbox" name="segments[{{ $i }}][services][]" value="{{ $slug }}" @checked(in_array($slug, $row['services'], true))>{{ $service['title'] }}</label>
            @endforeach
          </div>
        </div>
        <div class="mv">
          <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
          <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
          <button type="button" data-del title="Hapus tab" aria-label="Hapus tab">✕</button>
        </div>
      </div>
    @endforeach
  </div>
  <button type="button" class="add-ev" id="add-segment">+ Tambah tab</button>

  <template id="seg-tpl">
    <div class="ev seg-row" data-state="next">
      <span class="n"></span>
      <div class="seg-body">
        <input type="hidden" name="segments[__][key]" value="">
        <input class="seg-name" name="segments[__][label]" placeholder="Nama tab, mis. CCTV" maxlength="40" aria-label="Nama tab" required>
        <div class="sel-list">
          @foreach ($services as $slug => $service)
            <label><input type="checkbox" name="segments[__][services][]" value="{{ $slug }}">{{ $service['title'] }}</label>
          @endforeach
        </div>
      </div>
      <div class="mv">
        <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
        <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
        <button type="button" data-del title="Hapus tab" aria-label="Hapus tab">✕</button>
      </div>
    </div>
  </template>

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'segmen', 'custom' => $custom])
@endsection
