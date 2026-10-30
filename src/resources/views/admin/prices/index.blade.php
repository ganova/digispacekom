@extends('admin.layout')

@section('title', 'Daftar harga')
@section('heading', 'Daftar harga')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.prices.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tambah item</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px">Item ini tampil di halaman <a href="{{ route('prices.index') }}" target="_blank" rel="noopener">Cek Harga</a>. Harga bersifat estimasi, jadi tulis rentang bila perlu.</p>
<section class="card">
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari item harga…">
    <select name="category" onchange="this.form.submit()">
      <option value="">Semua kategori</option>
      @foreach (\App\Models\PriceItem::CATEGORIES as $key => $label)
        <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
      @endforeach
    </select>
    <button class="btn" type="submit">Cari</button>
  </form>

  @if ($items->isEmpty())
    <p class="empty">Belum ada item harga.</p>
  @else
    <div class="tbl-wrap">
      <table class="stack">
        <thead><tr><th>Kategori</th><th>Layanan</th><th class="num">Harga tampil</th><th></th></tr></thead>
        <tbody>
        @foreach ($items as $item)
          <tr>
            <td data-l="Kategori"><span class="badge">{{ \App\Models\PriceItem::CATEGORIES[$item->category] ?? $item->category }}</span></td>
            <td data-l="Layanan"><a class="t" href="{{ route('admin.prices.edit', $item) }}">{{ $item->name }}</a>@if ($item->note)<span class="sub">{{ $item->note }}</span>@endif</td>
            <td data-l="Harga tampil" class="num mono">{{ $item->priceLabel() }}</td>
            <td>
              <div class="row-acts">
                <a class="btn btn-sm" href="{{ route('admin.prices.edit', $item) }}">Ubah</a>
                <form method="post" action="{{ route('admin.prices.destroy', $item) }}" data-confirm="Hapus “{{ $item->name }}”?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @include('admin.partials.pager', ['paginator' => $items])
  @endif
</section>
@endsection
