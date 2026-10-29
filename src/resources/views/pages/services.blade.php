@extends('layouts.company')

@section('title', 'Layanan')
@section('description', 'Service laptop, PC, MacBook, printer, server & jaringan, upgrade PC, dan pengadaan perangkat untuk kantor dan rumah se-Jabodetabek.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Layanan',
    'title' => 'Satu tim untuk semua urusan komputer',
    'lead' => 'Untuk kantor, sekolah, instansi, dan rumah di seluruh '.config('site.area').'. Teknisi datang ke lokasi, biaya disepakati sebelum dikerjakan.',
])

<section class="b-sec">
  <div class="wrap b-cards">
    @foreach ($services as $slug => $s)
      <a class="b-svc" href="{{ route('services.show', $slug) }}">
        @include('partials.icon', ['name' => $s['icon']])
        <b>{{ $s['title'] }}</b>
        <span>{{ $s['short'] }}</span>
        <em>Lihat detail →</em>
      </a>
    @endforeach
  </div>
</section>
@endsection
