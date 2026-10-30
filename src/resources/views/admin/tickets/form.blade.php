@extends('admin.layout')

@php
    $editing = $ticket->exists;
    $states = \App\Http\Controllers\Admin\TicketController::STATES;
    $rows = old('events')
        ? collect(old('events'))->map(fn ($e) => (object) $e)->values()
        : $events;
@endphp
@section('title', $editing ? 'Tiket '.$ticket->code : 'Buat tiket servis')
@section('heading', $editing ? 'Tiket '.$ticket->code : 'Buat tiket servis')

@section('actions')
  <a class="btn" href="{{ route('admin.tickets.index') }}">← Kembali</a>
  @if ($editing)<a class="btn" href="{{ route('status', ['tiket' => $ticket->code]) }}" target="_blank" rel="noopener">Lihat tampilan pelanggan</a>@endif
@endsection

@section('content')
<form method="post" action="{{ $editing ? route('admin.tickets.update', $ticket) : route('admin.tickets.store') }}">
  @csrf
  @if ($editing) @method('put') @endif

  <section class="card">
    <h2>Data perangkat</h2>
    <p class="sub">Nama pelanggan tampil tersamar ke publik (mis. “Budi S****”).</p>
    <div class="grid g2">
      <x-admin.field name="code" label="Nomor tiket" :value="$ticket->code" required class="field mono" hint="Berikan nomor ini ke pelanggan." />
      <x-admin.field name="customer_name" label="Nama pelanggan" :value="$ticket->customer_name" required />
      <x-admin.field name="device" label="Perangkat" :value="$ticket->device" required placeholder="Laptop ASUS VivoBook 14" />
      <x-admin.field name="complaint" label="Keluhan" :value="$ticket->complaint" placeholder="Layar bergaris setelah jatuh" />
    </div>
  </section>

  <section class="card">
    <h2>Tahapan perbaikan</h2>
    <p class="sub">Urutan di sini adalah urutan yang dilihat pelanggan. Tandai satu tahap sebagai “Sedang berjalan”, yang sebelumnya “Selesai”.</p>

    <div class="events" id="events">
      @foreach ($rows as $i => $ev)
        <div class="ev" data-state="{{ $ev->state ?? 'next' }}">
          <span class="n">{{ $i + 1 }}</span>
          <input name="events[{{ $i }}][title]" value="{{ $ev->title ?? '' }}" placeholder="Nama tahap" aria-label="Nama tahap">
          <input name="events[{{ $i }}][note]" value="{{ $ev->note ?? '' }}" placeholder="Catatan (opsional)" aria-label="Catatan">
          <input type="datetime-local" name="events[{{ $i }}][occurred_at]" value="{{ ! empty($ev->occurred_at) ? \Illuminate\Support\Carbon::parse($ev->occurred_at)->format('Y-m-d\TH:i') : '' }}" aria-label="Waktu">
          <select name="events[{{ $i }}][state]" aria-label="Status tahap">
            @foreach ($states as $key => $label)<option value="{{ $key }}" @selected(($ev->state ?? 'next') === $key)>{{ $label }}</option>@endforeach
          </select>
          <div class="mv">
            <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
            <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
            <button type="button" data-del title="Hapus tahap" aria-label="Hapus tahap">✕</button>
          </div>
        </div>
      @endforeach
    </div>
    <button type="button" class="btn btn-sm" id="add-ev" style="margin-top:12px">+ Tambah tahap</button>

    <template id="ev-tpl">
      <div class="ev" data-state="next">
        <span class="n"></span>
        <input name="events[__][title]" placeholder="Nama tahap" aria-label="Nama tahap">
        <input name="events[__][note]" placeholder="Catatan (opsional)" aria-label="Catatan">
        <input type="datetime-local" name="events[__][occurred_at]" aria-label="Waktu">
        <select name="events[__][state]" aria-label="Status tahap">
          @foreach ($states as $key => $label)<option value="{{ $key }}" @selected($key === 'next')>{{ $label }}</option>@endforeach
        </select>
        <div class="mv">
          <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
          <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
          <button type="button" data-del title="Hapus tahap" aria-label="Hapus tahap">✕</button>
        </div>
      </div>
    </template>

    <div class="actions">
      <button class="btn btn-primary" type="submit">Simpan tiket</button>
      <a class="btn" href="{{ route('admin.tickets.index') }}">Batal</a>
    </div>
  </section>
</form>
@endsection
