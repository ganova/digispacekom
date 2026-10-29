@extends('layouts.pricing')

@section('title', 'Cek Status Servis')
@section('description', 'Cek status perbaikan perangkat Anda di '.config('site.name').' dengan nomor tiket servis.')

@push('meta')
<meta name="robots" content="noindex">
@endpush

@section('content')
<section class="c-hero c-hero-sm">
  <div class="c-blob" aria-hidden="true"><i></i><i></i></div>
  <div class="wrap">
    <span class="eyebrow fade-up" style="--fd:.05s">Cek status servis</span>
    <h1 class="split">{!! \App\Support\Site::split([['Pantau servis'], ['tanpa perlu chat.', 'hl']]) !!}</h1>
    <p class="lead fade-up" style="--fd:.5s">Lihat posisi perbaikan perangkat Anda kapan saja dengan nomor tiket.</p>
  </div>
</section>

<section class="c-sec wrap">
  @include('prices._tracker')
</section>
@endsection
