@extends('admin.layout')

@php $editing = $slug !== null; @endphp
@section('title', $editing ? 'Ubah layanan' : 'Tambah layanan')
@section('heading', $editing ? 'Ubah layanan' : 'Tambah layanan')

@section('actions')
  <a class="btn" href="{{ route('admin.services.index') }}">← Kembali</a>
  @if ($editing)<a class="btn" href="{{ route('services.show', $slug) }}" target="_blank" rel="noopener">Lihat halaman</a>@endif
@endsection

@section('content')
<form class="card" method="post" action="{{ $editing ? route('admin.services.update', $slug) : route('admin.services.store') }}">
  @csrf
  @if ($editing) @method('put') @endif

  <div class="grid g2">
    <x-admin.field name="title" label="Nama layanan" :value="$service['title'] ?? null" required />
    <x-admin.field name="icon" label="Ikon" :options="\App\Http\Controllers\Admin\ServiceController::ICONS" :value="$service['icon']" required />
    <x-admin.field name="short" label="Ringkasan satu kalimat" :value="$service['short'] ?? null" required class="span2 field" hint="Tampil di kartu layanan. Maksimal 140 karakter." maxlength="140" />
    <x-admin.field name="price_category" label="Kategori di Cek Harga" :options="['' => '— Tanpa tautan harga —'] + \App\Models\PriceItem::categories()" :value="$service['price_category']" hint="Menghubungkan tombol “Cek harga” di halaman layanan ke kategori ini." />
    <div></div>
    <x-admin.field name="intro" label="Pengantar" :rows="4" :value="$service['intro'] ?? null" required class="span2 field" />
    <x-admin.field name="problems" label="Masalah yang ditangani" :rows="7" :value="implode(PHP_EOL, $service['problems'])" class="span2 field" hint="Satu baris satu masalah. Boleh dikosongkan." />
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn" href="{{ route('admin.services.index') }}">Batal</a>
  </div>
</form>
@endsection
