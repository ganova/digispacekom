@extends('admin.layout')

@section('title', 'Tentang, visi & misi')
@section('heading', 'Tentang, visi & misi')

@section('content')
<p class="note" style="margin-bottom:16px"><span>Tampil di halaman <a href="{{ route('about') }}" target="_blank" rel="noopener">Tentang</a> dan di halaman <a href="{{ route('contact') }}" target="_blank" rel="noopener">Kontak</a> (di bawah kartu kontak). Isi awalnya contoh; ganti dengan profil resmi perusahaan.</span></p>

<form class="card" method="post" action="{{ route('admin.about.update') }}">
  @csrf @method('put')
  <div class="grid">
    <x-admin.field name="about_lead" label="Tentang singkat" :rows="3" :value="config('site.about_lead')" required maxlength="400" hint="Kalimat pembuka yang tampil menonjol. Maksimal 400 karakter." />
    <x-admin.field name="about_body" label="Uraian lengkap (opsional)" :rows="6" :value="config('site.about_body')" maxlength="3000" hint="Pisahkan paragraf dengan satu baris kosong." />
    <x-admin.field name="vision" label="Visi" :rows="3" :value="config('site.vision')" required maxlength="500" />
    <x-admin.field name="mission" label="Misi" :rows="6" :value="implode(PHP_EOL, config('site.mission'))" required hint="Satu baris satu butir misi." />
  </div>
  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'tentang', 'custom' => $custom])
@endsection
