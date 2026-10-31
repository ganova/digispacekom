@extends('admin.layout')

@section('title', 'Profil & kontak')
@section('heading', 'Profil & kontak')

@section('content')
<form class="card" method="post" enctype="multipart/form-data" action="{{ route('admin.profile.update') }}">
  @csrf @method('put')
  <h2>Data perusahaan</h2>
  <p class="sub">Tampil di header, footer, halaman Kontak, dan tombol WhatsApp di seluruh situs.</p>

  <div class="logo-box">
    <div>
      <h3>Logo perusahaan</h3>
      <p class="muted" style="margin:4px 0 14px;font-size:14px">Tampil di header dan footer situs. PNG, JPG, atau WebP, maksimal 2 MB. Latar transparan paling rapi. Tanpa unggahan, situs memakai logo sementara.</p>
    </div>
    <div class="grid g2">
      <div class="field">
        <label for="f-logo">Logo untuk latar terang</label>
        <div class="img-pick">
          <span class="img-prev img-wide" id="logo-prev">@if (config('site.logo'))<img src="{{ \App\Support\Images::url('brand', config('site.logo')) }}" alt="Logo">@else<em>Belum ada logo</em>@endif</span>
          <div>
            <label class="btn btn-sm cl-pick"><input id="f-logo" type="file" name="logo" accept="image/png,image/jpeg,image/webp" hidden data-preview="#logo-prev">{{ config('site.logo') ? 'Ganti logo' : 'Unggah logo' }}</label>
            @if (config('site.logo'))<label class="check cl-rm" style="margin-top:8px"><input type="checkbox" name="logo_remove" value="1"> Hapus logo</label>@endif
          </div>
        </div>
        @error('logo')<em class="err">{{ $message }}</em>@enderror
      </div>
      <div class="field">
        <label for="f-logo_dark">Logo untuk latar gelap <span class="muted" style="font-weight:400">(opsional)</span></label>
        <div class="img-pick">
          <span class="img-prev img-wide img-darkbg" id="logo-dark-prev">@if (config('site.logo_dark'))<img src="{{ \App\Support\Images::url('brand', config('site.logo_dark')) }}" alt="Logo latar gelap">@else<em>Memakai logo terang di kotak putih</em>@endif</span>
          <div>
            <label class="btn btn-sm cl-pick"><input id="f-logo_dark" type="file" name="logo_dark" accept="image/png,image/jpeg,image/webp" hidden data-preview="#logo-dark-prev">{{ config('site.logo_dark') ? 'Ganti logo' : 'Unggah logo' }}</label>
            @if (config('site.logo_dark'))<label class="check cl-rm" style="margin-top:8px"><input type="checkbox" name="logo_dark_remove" value="1"> Hapus logo</label>@endif
          </div>
        </div>
        <small>Dipakai di atas hero gelap, footer, dan mode gelap. Bila kosong, logo terang ditampilkan di kotak putih.</small>
        @error('logo_dark')<em class="err">{{ $message }}</em>@enderror
      </div>
    </div>
  </div>

  <div class="grid g2" style="margin-top:22px">
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
