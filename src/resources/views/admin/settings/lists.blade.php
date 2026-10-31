@extends('admin.layout')

@section('title', 'Wilayah & langkah')
@section('heading', 'Wilayah & langkah')

@section('content')
@php
    $steps = collect(config('site.steps'))->map(fn ($s) => $s[0].' | '.($s[1] ?? ''))->implode("\n");
@endphp
<form class="card" method="post" action="{{ route('admin.lists.update') }}">
  @csrf @method('put')
  <h2>Daftar tampilan</h2>
  <p class="sub">Satu baris satu butir. Baris kosong diabaikan.</p>

  <div class="grid">
    <x-admin.field name="service_areas" label="Wilayah layanan" :rows="7" :value="implode(PHP_EOL, config('site.service_areas'))" />
    <x-admin.field name="steps" label="Langkah layanan" :rows="5" :value="$steps" required hint="Format: Judul | penjelasan singkat. Contoh: Booking | Isi form atau chat WhatsApp." />
  </div>

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'daftar', 'custom' => $custom])
@endsection
