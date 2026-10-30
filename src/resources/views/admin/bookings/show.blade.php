@extends('admin.layout')

@section('title', 'Permintaan #'.$booking->id)
@section('heading', 'Permintaan #'.$booking->id)

@section('actions')
  <a class="btn" href="{{ route('admin.bookings.index') }}">← Kembali</a>
@endsection

@section('content')
<div class="two">
  <section class="card">
    <h2>{{ $booking->type === 'penawaran' ? 'Minta penawaran' : 'Booking teknisi' }}</h2>
    <p class="sub">Masuk {{ $booking->created_at->translatedFormat('l, j F Y · H:i') }}@if ($booking->source) · dari halaman <span class="mono">{{ $booking->source }}</span>@endif</p>
    <dl class="kv">
      <dt>Nama</dt><dd>{{ $booking->name ?: '—' }}</dd>
      <dt>Instansi</dt><dd>{{ $booking->organization ?: '—' }}</dd>
      <dt>Perangkat</dt><dd>{{ $booking->device ?: '—' }}</dd>
      @if ($booking->units)<dt>Jumlah unit</dt><dd>{{ $booking->units }}</dd>@endif
      <dt>{{ $booking->type === 'penawaran' ? 'Kebutuhan' : 'Keluhan' }}</dt><dd>{{ $booking->complaint }}</dd>
      <dt>Lokasi</dt><dd>{{ $booking->location ?: '—' }}</dd>
    </dl>
    <p class="note" style="margin-top:18px">Pengunjung sudah diarahkan ke WhatsApp dengan pesan yang terisi (Ref #{{ $booking->id }}). Data kontaknya ada di chat WhatsApp, bukan di sini.</p>
  </section>

  <section class="card">
    <h2>Tindak lanjut</h2>
    <p class="sub">Catat sudah sampai mana permintaan ini.</p>
    <form method="post" action="{{ route('admin.bookings.update', $booking) }}" class="grid">
      @csrf @method('put')
      <x-admin.field name="status" label="Status" :options="\App\Models\Booking::STATUSES" :value="$booking->status" />
      <x-admin.field name="admin_note" label="Catatan internal" :rows="5" :value="$booking->admin_note" hint="Hanya terlihat oleh admin." />
      <button class="btn btn-primary" type="submit">Simpan</button>
    </form>
    <form method="post" action="{{ route('admin.bookings.destroy', $booking) }}" data-confirm="Hapus permintaan ini secara permanen?" style="margin-top:14px">
      @csrf @method('delete')
      <button class="btn btn-danger btn-sm" type="submit">Hapus permintaan</button>
    </form>
  </section>
</div>
@endsection
