@extends('admin.layout')

@php $editing = $item->exists; @endphp
@section('title', $editing ? 'Ubah item harga' : 'Tambah item harga')
@section('heading', $editing ? 'Ubah item harga' : 'Tambah item harga')

@section('actions')
  <a class="btn" href="{{ route('admin.prices.index') }}">← Kembali</a>
@endsection

@section('content')
<form class="card" method="post" action="{{ $editing ? route('admin.prices.update', $item) : route('admin.prices.store') }}">
  @csrf
  @if ($editing) @method('put') @endif

  <div class="grid g2">
    <x-admin.field name="category" label="Kategori" :options="\App\Models\PriceItem::CATEGORIES" :value="$item->category" required />
    <x-admin.field name="name" label="Nama layanan" :value="$item->name" required placeholder="Contoh: Ganti baterai laptop" />
    <x-admin.field name="price_min" label="Harga mulai (Rp)" type="number" min="0" step="1000" :value="$item->price_min" required inputmode="numeric" />
    <x-admin.field name="price_max" label="Harga sampai (Rp)" type="number" min="0" step="1000" :value="$item->price_max" hint="Kosongkan bila harga pasti." inputmode="numeric" />
    <x-admin.field name="price_suffix" label="Keterangan harga" :value="$item->price_suffix" hint="Contoh: + SSD, per titik. Tampil setelah angka." />
    <x-admin.field name="note" label="Catatan di bawah nama" :value="$item->note" hint="Contoh: Estimasi 1–2 hari." />
    <x-admin.field name="keywords" label="Kata kunci pencarian" :value="$item->keywords" class="span2 field" hint="Pisahkan dengan spasi. Membantu pengunjung menemukan item lewat kolom cari. Contoh: mati hidup nyala." />
    <x-admin.field name="sort" label="Urutan" type="number" min="0" :value="$item->sort" hint="Angka kecil tampil lebih dulu dalam kategorinya." />
    <div class="field">
      <label>Akan tampil sebagai</label>
      <span class="preview-chip" id="price-chip">—</span>
    </div>
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn" href="{{ route('admin.prices.index') }}">Batal</a>
  </div>
</form>
@endsection
