@extends('admin.layout')

@section('title', 'Klien, wilayah, langkah')
@section('heading', 'Klien, wilayah, langkah')

@section('content')
@php
    $steps = collect(config('site.steps'))->map(fn ($s) => $s[0].' | '.($s[1] ?? ''))->implode("\n");
@endphp
<form class="card" method="post" action="{{ route('admin.lists.update') }}">
  @csrf @method('put')
  <h2>Daftar tampilan</h2>
  <p class="sub">Satu baris satu butir. Baris kosong diabaikan.</p>

  <div class="grid">
    <x-admin.field name="clients" label="Klien yang dipercaya" :rows="7" :value="implode("\n", config('site.clients'))" hint="Tampil di beranda dan halaman Tentang. Pakai nama klien yang sudah setuju namanya dipublikasikan." />
    <x-admin.field name="service_areas" label="Wilayah layanan" :rows="7" :value="implode("\n", config('site.service_areas'))" />
    <x-admin.field name="steps" label="Langkah layanan" :rows="5" :value="$steps" required hint="Format: Judul | penjelasan singkat. Contoh: Booking | Isi form atau chat WhatsApp." />
  </div>

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'daftar', 'custom' => $custom])
@endsection
