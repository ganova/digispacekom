@extends('layouts.pricing')

@section('title', 'Cek Status Servis')
@section('description', 'Cek status perbaikan perangkat Anda di '.config('site.name').' dengan nomor tiket servis.')

@push('meta')
<meta name="robots" content="noindex">
@endpush

@section('content')
<section class="c-hero c-hero-sm wrap">
  <div>
    <h1>Sudah sampai mana <em>servisnya?</em></h1>
    <p>Pantau perbaikan perangkat tanpa perlu chat menanyakan status.</p>
  </div>
</section>

<section class="c-sec wrap">
  @include('prices._tracker')
</section>
@endsection
