@extends('admin.layout')

@section('title', 'Landing page iklan')
@section('heading', 'Landing page iklan')

@section('content')
<p class="note" style="margin-bottom:16px">Halaman khusus untuk tujuan iklan (Google Ads, Instagram, dan lainnya). Alamatnya tetap; Anda mengubah isinya.</p>
<div class="grid g2">
  @foreach ($landings as $slug => $page)
    <section class="card">
      <h2>{{ $page['headline'] }} <span class="muted">{{ $page['highlight'] }}</span></h2>
      <p class="sub mono">/{{ $slug }}</p>
      <p>{{ $page['lead'] }}</p>
      <div class="actions" style="margin-top:14px;padding-top:14px">
        <a class="btn btn-primary btn-sm" href="{{ route('admin.landings.edit', $slug) }}">Ubah isi</a>
        <a class="btn btn-sm" href="{{ route('landing', $slug) }}" target="_blank" rel="noopener">Lihat halaman</a>
      </div>
    </section>
  @endforeach
</div>
@include('admin.partials.reset', ['section' => 'landing', 'custom' => $custom])
@endsection
