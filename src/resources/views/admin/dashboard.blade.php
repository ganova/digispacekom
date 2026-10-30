@extends('admin.layout')

@section('title', 'Dasbor')
@section('heading', 'Halo, '.explode(' ', auth()->user()->name)[0])

@section('content')
<div class="stats">
  <a class="stat {{ $newBookings ? 'hot' : '' }}" href="{{ route('admin.bookings.index', ['status' => 'baru']) }}"><b>{{ $newBookings }}</b><span>Permintaan baru</span></a>
  <a class="stat" href="{{ route('admin.bookings.index') }}"><b>{{ $weekBookings }}</b><span>Permintaan 7 hari terakhir</span></a>
  <a class="stat" href="{{ route('admin.tickets.index') }}"><b>{{ $tickets }}</b><span>Tiket servis</span></a>
  <a class="stat" href="{{ route('admin.prices.index') }}"><b>{{ $prices }}</b><span>Item daftar harga</span></a>
  <a class="stat" href="{{ route('admin.articles.index') }}"><b>{{ $articles }}</b><span>Artikel{{ $drafts ? " ({$drafts} draf)" : '' }}</span></a>
</div>

<div class="two">
  <section class="card">
    <h2>Permintaan terbaru</h2>
    <p class="sub">Dari form booking dan minta penawaran di situs.</p>
    @if ($latest->isEmpty())
      <p class="empty">Belum ada permintaan masuk.</p>
    @else
      <div class="tbl-wrap">
        <table class="stack">
          <thead><tr><th>Waktu</th><th>Dari</th><th>Isi</th><th>Status</th></tr></thead>
          <tbody>
          @foreach ($latest as $b)
            <tr>
              <td data-l="Waktu" class="muted">{{ $b->created_at->diffForHumans() }}</td>
              <td data-l="Dari"><a class="t" href="{{ route('admin.bookings.show', $b) }}">{{ $b->name ?: $b->organization ?: 'Tanpa nama' }}</a><span class="sub">{{ $b->type === 'penawaran' ? 'Penawaran' : 'Booking' }}</span></td>
              <td data-l="Isi">{{ \Illuminate\Support\Str::limit($b->complaint, 70) }}</td>
              <td data-l="Status"><span class="badge {{ $b->status }}">{{ \App\Models\Booking::STATUSES[$b->status] ?? $b->status }}</span></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </section>

  <section class="card">
    <h2>Pintasan</h2>
    <p class="sub">Pekerjaan yang paling sering dilakukan.</p>
    <div class="grid">
      <a class="btn" href="{{ route('admin.tickets.create') }}">+ Buat tiket servis</a>
      <a class="btn" href="{{ route('admin.prices.create') }}">+ Tambah item harga</a>
      <a class="btn" href="{{ route('admin.articles.create') }}">+ Tulis artikel</a>
      <a class="btn" href="{{ route('admin.profile') }}">Ubah nomor WA & kontak</a>
    </div>
  </section>
</div>
@endsection
