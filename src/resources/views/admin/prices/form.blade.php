@extends('admin.layout')

@php $editing = $item->exists; @endphp
@section('title', $editing ? 'Ubah item harga' : 'Tambah item harga')
@section('heading', $editing ? 'Ubah item harga' : 'Tambah item harga')

@section('actions')
  <a class="btn" href="{{ route('admin.prices.index') }}">← Kembali</a>
@endsection

@section('content')
<form class="card" method="post" enctype="multipart/form-data" action="{{ $editing ? route('admin.prices.update', $item) : route('admin.prices.store') }}">
  @csrf
  @if ($editing) @method('put') @endif

  <div class="grid g2">
    <div>
      <x-admin.field name="category" label="Kategori" :options="\App\Models\PriceItem::categories()" :value="$item->category" required />
      <small class="muted" style="display:block;margin-top:6px">Butuh kategori baru (mis. CCTV)? <a href="{{ route('admin.price-categories') }}">Kelola kategori</a>.</small>
    </div>
    <x-admin.field name="name" label="Nama item / layanan" :value="$item->name" required placeholder="Contoh: Ganti baterai laptop, atau SSD 256 GB" />
    <x-admin.field name="price_min" label="Harga mulai (Rp)" type="number" min="0" step="1000" :value="$item->price_min" required inputmode="numeric" />
    <x-admin.field name="price_max" label="Harga sampai (Rp)" type="number" min="0" step="1000" :value="$item->price_max" hint="Kosongkan bila harga pasti." inputmode="numeric" />
    <x-admin.field name="price_suffix" label="Keterangan harga" :value="$item->price_suffix" hint="Contoh: + SSD, per titik. Tampil setelah angka." />
    <x-admin.field name="note" label="Catatan di bawah nama" :value="$item->note" hint="Contoh: Estimasi 1–2 hari, atau garansi 1 bulan." />
    <x-admin.field name="stock" label="Status stok (untuk suku cadang)" :options="['' => 'Tidak ditampilkan'] + \App\Models\PriceItem::STOCK" :value="$item->stock" hint="“Ready stok” memberi lencana hijau di Cek Harga." />
    <x-admin.field name="sort" label="Urutan" type="number" min="0" :value="$item->sort" hint="Angka kecil tampil lebih dulu." />

    <div class="field span2">
      <label for="f-photo">Foto (opsional)</label>
      <div class="img-pick">
        <span class="img-prev" id="photo-prev">@if ($item->image)<img src="{{ \App\Support\Images::url('prices', $item->image) }}" alt="Foto {{ $item->name }}">@else<em>Belum ada foto</em>@endif</span>
        <div>
          <label class="btn btn-sm cl-pick"><input id="f-photo" type="file" name="photo" accept="image/png,image/jpeg,image/webp" hidden data-preview="#photo-prev">{{ $item->image ? 'Ganti foto' : 'Unggah foto' }}</label>
          @if ($item->image)<label class="check cl-rm" style="margin-top:8px"><input type="checkbox" name="photo_remove" value="1"> Hapus foto</label>@endif
          <small style="display:block;margin-top:8px">JPG, PNG, atau WebP, maksimal 6 MB. Foto besar otomatis dikecilkan.</small>
        </div>
      </div>
      @error('photo')<em class="err">{{ $message }}</em>@enderror
    </div>

    <x-admin.field name="keywords" label="Kata kunci pencarian" :value="$item->keywords" class="span2 field" hint="Pisahkan dengan spasi. Membantu pengunjung menemukan item lewat kolom cari. Contoh: mati hidup nyala." />
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
