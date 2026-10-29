@extends('layouts.company')

@section('title', 'Tentang & Klien')
@section('description', config('site.name').' melayani IT support, service komputer, dan jaringan untuk kantor, sekolah, dan instansi se-Jabodetabek.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Tentang kami',
    'title' => config('site.name'),
    'lead' => 'Perusahaan IT support di '.config('site.city').' yang melayani kantor, sekolah, instansi, dan rumah di seluruh '.config('site.area').'.',
])

<section class="b-sec">
  <div class="wrap b-detail">
    <div class="b-prose">
      {{-- Teks contoh — ganti dengan profil resmi perusahaan --}}
      <p>Kami membantu organisasi menjaga perangkat IT tetap berjalan: dari laptop staf, printer, dan server, sampai jaringan gedung dan CCTV. Untuk kantor yang tidak memiliki tim IT sendiri, kami menyediakan kontrak maintenance dengan kunjungan rutin dan laporan kondisi aset setiap bulan.</p>
      <p>Pelanggan rumahan juga kami layani lewat service panggilan: teknisi datang, mengecek di tempat, dan biaya disepakati sebelum dikerjakan.</p>
      <h2>Cara kami bekerja</h2>
      <ul class="b-checks">
        <li>Biaya dan lingkup pekerjaan disepakati sebelum dikerjakan</li>
        <li>Perangkat yang dibawa ke workshop diberi tanda terima dan nomor tiket</li>
        <li>Garansi servis untuk setiap perbaikan</li>
        <li>Dokumen penawaran resmi untuk kantor dan instansi</li>
      </ul>
    </div>
    <aside class="b-card b-aside">
      <h2>Area layanan</h2>
      <ul class="b-areas">@foreach (config('site.service_areas') as $area)<li>{{ $area }}</li>@endforeach</ul>
    </aside>
  </div>
</section>

<section class="b-sec b-sec-alt" id="klien">
  <div class="wrap">
    <h2>Dipercaya kantor &amp; instansi</h2>
    <div class="b-clients">
      @foreach (config('site.clients') as $client)<div>{{ $client }}</div>@endforeach
    </div>
  </div>
</section>
@endsection
