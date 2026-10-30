@extends('admin.layout')

@section('title', 'Tiket servis')
@section('heading', 'Tiket servis')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.tickets.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Buat tiket</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px">Pelanggan mengecek status di <a href="{{ route('status') }}" target="_blank" rel="noopener">Cek Status Servis</a> dengan nomor tiket. Namanya ditampilkan tersamar di sana.</p>
<section class="card">
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nomor tiket, nama, atau perangkat…">
    <button class="btn" type="submit">Cari</button>
  </form>

  @if ($tickets->isEmpty())
    <p class="empty">Belum ada tiket.</p>
  @else
    <div class="tbl-wrap">
      <table class="stack">
        <thead><tr><th>Tiket</th><th>Pelanggan</th><th>Perangkat</th><th>Tahap sekarang</th><th></th></tr></thead>
        <tbody>
        @foreach ($tickets as $t)
          <tr>
            <td data-l="Tiket"><a class="t mono" href="{{ route('admin.tickets.edit', $t) }}">{{ $t->code }}</a><span class="sub">{{ $t->created_at->translatedFormat('j M Y') }}</span></td>
            <td data-l="Pelanggan">{{ $t->customer_name }}</td>
            <td data-l="Perangkat">{{ $t->device }}@if ($t->complaint)<span class="sub">{{ $t->complaint }}</span>@endif</td>
            <td data-l="Tahap sekarang">@if ($now = $t->events->first())<span class="badge now">{{ $now->title }}</span>@else<span class="muted">—</span>@endif</td>
            <td>
              <div class="row-acts">
                <a class="btn btn-sm" href="{{ route('status', ['tiket' => $t->code]) }}" target="_blank" rel="noopener">Lihat</a>
                <a class="btn btn-sm" href="{{ route('admin.tickets.edit', $t) }}">Ubah</a>
                <form method="post" action="{{ route('admin.tickets.destroy', $t) }}" data-confirm="Hapus tiket {{ $t->code }}?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @include('admin.partials.pager', ['paginator' => $tickets])
  @endif
</section>
@endsection
