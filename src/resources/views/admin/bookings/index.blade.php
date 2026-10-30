@extends('admin.layout')

@section('title', 'Permintaan masuk')
@section('heading', 'Permintaan masuk')

@section('actions')
  <a class="btn" href="{{ route('admin.bookings.export', request()->query()) }}">Unduh CSV</a>
@endsection

@section('content')
@php $statuses = \App\Models\Booking::STATUSES; @endphp
<div class="chips">
  <a href="{{ route('admin.bookings.index', request()->except(['status', 'page'])) }}" @class(['on' => ! request('status')])>Semua<em>{{ $counts->sum() }}</em></a>
  @foreach ($statuses as $key => $label)
    <a href="{{ route('admin.bookings.index', ['status' => $key] + request()->except(['status', 'page'])) }}" @class(['on' => request('status') === $key])>{{ $label }}<em>{{ $counts[$key] ?? 0 }}</em></a>
  @endforeach
</div>

<section class="card">
  <form class="filters" method="get">
    @if (request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama, instansi, keluhan, lokasi…">
    <select name="type" onchange="this.form.submit()">
      <option value="">Semua jenis</option>
      <option value="booking" @selected(request('type') === 'booking')>Booking teknisi</option>
      <option value="penawaran" @selected(request('type') === 'penawaran')>Minta penawaran</option>
    </select>
    <button class="btn" type="submit">Cari</button>
  </form>

  @if ($bookings->isEmpty())
    <p class="empty">Tidak ada permintaan yang cocok.</p>
  @else
    <div class="tbl-wrap">
      <table class="stack">
        <thead><tr><th>Masuk</th><th>Dari</th><th>Kebutuhan</th><th>Lokasi</th><th>Status</th></tr></thead>
        <tbody>
        @foreach ($bookings as $b)
          <tr>
            <td data-l="Masuk" class="muted" style="white-space:nowrap">{{ $b->created_at->translatedFormat('j M, H:i') }}</td>
            <td data-l="Dari">
              <a class="t" href="{{ route('admin.bookings.show', $b) }}">{{ $b->name ?: 'Tanpa nama' }}</a>
              <span class="sub">{{ $b->type === 'penawaran' ? 'Penawaran' : 'Booking' }}@if ($b->organization) · {{ $b->organization }}@endif</span>
            </td>
            <td data-l="Kebutuhan">{{ $b->device }}@if ($b->device && $b->complaint) — @endif{{ \Illuminate\Support\Str::limit($b->complaint, 80) }}</td>
            <td data-l="Lokasi">{{ $b->location ?: '—' }}</td>
            <td data-l="Status"><span class="badge {{ $b->status }}">{{ $statuses[$b->status] ?? $b->status }}</span></td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @include('admin.partials.pager', ['paginator' => $bookings])
  @endif
</section>
@endsection
