@extends('layouts.company')

@section('title', 'Tentang & Klien')
@section('description', config('site.name').' melayani IT support, service komputer, dan jaringan untuk kantor, sekolah, dan instansi se-Jabodetabek.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Tentang kami',
    'titleParts' => [['Menjaga IT Anda'], ['tetap berjalan.', 'grad']],
    'lead' => config('site.name').' adalah perusahaan IT support di '.config('site.city').' yang melayani kantor, sekolah, instansi, dan rumah di seluruh '.config('site.area').'.',
])

<section class="sec">
  <div class="wrap detail">
    <div class="prose rv">
      {{-- Teks contoh — ganti dengan profil resmi perusahaan --}}
      <p class="prose-lead">Kami membantu organisasi menjaga perangkat IT tetap berjalan: dari laptop staf, printer, dan server, sampai jaringan gedung dan CCTV.</p>
      <p>Untuk kantor yang tidak memiliki tim IT sendiri, kami menyediakan kontrak maintenance dengan kunjungan rutin dan laporan kondisi aset setiap bulan. Pelanggan rumahan juga kami layani lewat service panggilan: teknisi datang, mengecek di tempat, dan biaya disepakati sebelum dikerjakan.</p>
      <h2>Cara kami bekerja</h2>
      <ul class="checks checks-1">
        <li><span class="ok" aria-hidden="true">✓</span>Biaya dan lingkup pekerjaan disepakati sebelum dikerjakan</li>
        <li><span class="ok" aria-hidden="true">✓</span>Perangkat yang dibawa ke workshop diberi tanda terima dan nomor tiket</li>
        <li><span class="ok" aria-hidden="true">✓</span>Garansi servis untuk setiap perbaikan</li>
        <li><span class="ok" aria-hidden="true">✓</span>Dokumen penawaran resmi untuk kantor dan instansi</li>
      </ul>
    </div>
    <aside class="aside rv">
      <span class="ic-box">@include('partials.icon', ['name' => 'pin'])</span>
      <h2>Area layanan</h2>
      <ul class="areas">@foreach (config('site.service_areas') as $area)<li>{{ $area }}</li>@endforeach</ul>
    </aside>
  </div>
</section>

<section class="sec sec-dark" id="klien">
  <div class="aurora aurora-soft" aria-hidden="true"><i></i><i></i></div>
  <div class="wrap">
    <div class="sec-hd rv"><span class="eyebrow eyebrow-inv">Klien</span><h2>Dipercaya kantor &amp; instansi.</h2></div>
    <div class="clients" id="klien">
      @foreach (\App\Support\Site::clients() as $client)<div class="spot rv" style="--d:{{ $loop->index }}">@if ($client['logo_url'])<i class="cl-chip cl-chip-lg"><img src="{{ $client['logo_url'] }}" alt="{{ $client['name'] }}" loading="lazy" decoding="async"></i><small>{{ $client['name'] }}</small>@else{{ $client["name"] }} @endif</div>@endforeach
    </div>
  </div>
</section>
@endsection
