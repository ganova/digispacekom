@extends('admin.layout')

@section('title', 'Kategori harga')
@section('heading', 'Kategori harga')

@section('actions')
  <a class="btn" href="{{ route('admin.prices.index') }}">← Daftar harga</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px"><span>Kategori tampil sebagai tab di halaman <a href="{{ route('prices.index') }}" target="_blank" rel="noopener">Cek Harga</a>. Contoh yang bisa ditambah: <strong>Sparepart Ready</strong>, CCTV, Game Station. Kategori yang masih dipakai item tidak bisa dihapus.</span></p>

<section class="card">
  <div class="card-head"><div><h2>Daftar kategori</h2><p class="sub">Urutan di sini adalah urutan tab. Ubah nama langsung di kolomnya.</p></div></div>
  <div class="tbl-wrap">
    <table class="stack">
      <thead><tr><th>Nama kategori</th><th>Item</th><th></th></tr></thead>
      <tbody>
      @foreach ($categories as $key => $label)
        <tr>
          <td data-l="Nama kategori">
            <form method="post" action="{{ route('admin.price-categories.update', $key) }}" class="inline-edit" data-no-track>
              @csrf @method('put')
              <input name="label" value="{{ $label }}" maxlength="30" required aria-label="Nama kategori {{ $label }}">
              <button class="btn btn-sm" type="submit">Simpan</button>
            </form>
          </td>
          <td data-l="Item" class="mono">{{ $counts[$key] ?? 0 }}</td>
          <td>
            <div class="row-acts">
              <form method="post" action="{{ route('admin.price-categories.move', $key) }}">@csrf<input type="hidden" name="dir" value="up"><button class="btn btn-sm" type="submit" aria-label="Naikkan" @disabled($loop->first)>↑</button></form>
              <form method="post" action="{{ route('admin.price-categories.move', $key) }}">@csrf<input type="hidden" name="dir" value="down"><button class="btn btn-sm" type="submit" aria-label="Turunkan" @disabled($loop->last)>↓</button></form>
              <form method="post" action="{{ route('admin.price-categories.destroy', $key) }}" data-confirm="Hapus kategori “{{ $label }}”?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
            </div>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</section>

<section class="card">
  <h2>Tambah kategori</h2>
  <form method="post" action="{{ route('admin.price-categories.store') }}" class="inline-edit" data-no-track style="margin-top:12px">
    @csrf
    <input name="label" placeholder="Contoh: Sparepart Ready" maxlength="30" required aria-label="Nama kategori baru" value="{{ old('label') }}">
    <button class="btn btn-primary" type="submit">+ Tambah</button>
  </form>
  @error('label')<em class="err" style="color:var(--danger);font-style:normal;display:block;margin-top:8px">{{ $message }}</em>@enderror
</section>
@endsection
