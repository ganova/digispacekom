@extends('layouts.company')

@section('title', 'Layanan')
@section('description', 'Service laptop, PC, MacBook, printer, server & jaringan, upgrade PC, dan pengadaan perangkat untuk kantor dan rumah se-Jabodetabek.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Layanan',
    'titleParts' => [['Satu tim untuk semua'], ['urusan komputer.', 'grad']],
    'lead' => 'Untuk kantor, sekolah, instansi, dan rumah di seluruh '.config('site.area').'. Teknisi datang ke lokasi, biaya disepakati sebelum dikerjakan.',
])

<section class="sec">
  <div class="wrap bento bento-lg">
    @foreach ($services as $slug => $s)
      <a class="card spot rv" style="--d:{{ $loop->index }}" href="{{ route('services.show', $slug) }}">
        <span class="ic-box">@include('partials.icon', ['name' => $s['icon']])</span>
        <b>{{ $s['title'] }}</b>
        <span>{{ $s['short'] }}</span>
        <ul class="card-tags">@foreach (array_slice($s['problems'], 0, 3) as $p)<li>{{ $p }}</li>@endforeach</ul>
        <i class="card-go" aria-hidden="true">→</i>
      </a>
    @endforeach
  </div>
</section>
@endsection
