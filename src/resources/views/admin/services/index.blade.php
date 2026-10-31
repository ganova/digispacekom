@extends('admin.layout')

@section('title', 'Layanan')
@section('heading', 'Layanan')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.services.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tambah layanan</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px">Setiap layanan punya halaman sendiri di <a href="{{ route('services.index') }}" target="_blank" rel="noopener">/layanan</a>. Urutan di sini adalah urutan tampil. Pilihan layanan per tab beranda diatur di menu <a href="{{ route('admin.segments') }}">Tab beranda</a>.</p>
<section class="card">
  <div class="tbl-wrap">
    <table class="stack">
      <thead><tr><th>Layanan</th><th>Ringkasan</th><th>Kategori harga</th><th></th></tr></thead>
      <tbody>
      @foreach ($services as $slug => $s)
        <tr>
          <td data-l="Layanan"><a class="t" href="{{ route('admin.services.edit', $slug) }}">{{ $s['title'] }}</a><span class="sub mono">/layanan/{{ $slug }}</span></td>
          <td data-l="Ringkasan">{{ $s['short'] }}</td>
          <td data-l="Kategori harga">{{ $s['price_category'] ? (\App\Models\PriceItem::categories()[$s['price_category']] ?? $s['price_category']) : '—' }}</td>
          <td>
            <div class="row-acts">
              <form method="post" action="{{ route('admin.services.move', $slug) }}">@csrf<input type="hidden" name="dir" value="up"><button class="btn btn-sm" type="submit" aria-label="Naikkan" @disabled($loop->first)>↑</button></form>
              <form method="post" action="{{ route('admin.services.move', $slug) }}">@csrf<input type="hidden" name="dir" value="down"><button class="btn btn-sm" type="submit" aria-label="Turunkan" @disabled($loop->last)>↓</button></form>
              <a class="btn btn-sm" href="{{ route('admin.services.edit', $slug) }}">Ubah</a>
              <form method="post" action="{{ route('admin.services.destroy', $slug) }}" data-confirm="Hapus layanan “{{ $s['title'] }}”? Halamannya tidak bisa dibuka lagi.">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
            </div>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</section>
@include('admin.partials.reset', ['section' => 'layanan', 'custom' => $custom])
@endsection
