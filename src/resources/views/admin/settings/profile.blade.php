@extends('admin.layout')

@section('title', 'Profil & kontak')
@section('heading', 'Profil & kontak')

@section('content')
<form class="card" method="post" action="{{ route('admin.profile.update') }}">
  @csrf @method('put')
  <h2>Data perusahaan</h2>
  <p class="sub">Tampil di header, footer, halaman Kontak, dan tombol WhatsApp di seluruh situs.</p>

  <div class="grid g2">
    <x-admin.field name="name" label="Nama perusahaan" :value="config('site.name')" required />
    <x-admin.field name="short" label="Nama singkat" :value="config('site.short')" required hint="Dipakai di judul tab browser." />
    <x-admin.field name="tagline" label="Tagline" :value="config('site.tagline')" required class="span2 field" />
    <x-admin.field name="phone" label="Telepon" :value="config('site.phone')" required />
    <x-admin.field name="whatsapp" label="Nomor WhatsApp" :value="config('site.whatsapp')" required inputmode="tel" hint="Boleh diketik 0813-8502-8980; otomatis diubah ke format 62…" />
    <x-admin.field name="email" label="Email" type="email" :value="config('site.email')" required />
    <x-admin.field name="hours" label="Jam operasional" :value="config('site.hours')" required />
    <x-admin.field name="city" label="Kota" :value="config('site.city')" required />
    <x-admin.field name="area" label="Wilayah layanan" :value="config('site.area')" required />
    <x-admin.field name="address" label="Alamat" :value="config('site.address')" required class="span2 field" />
    <x-admin.field name="maps_query" label="Kata kunci Google Maps" :value="config('site.maps_query')" required class="span2 field" hint="Teks yang dicari di peta pada halaman Kontak." />
  </div>

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'profil', 'custom' => $custom])
@endsection
