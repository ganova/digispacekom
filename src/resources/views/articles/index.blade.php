@extends('layouts.company')

@section('title', 'Artikel')
@section('description', 'Tips merawat laptop, printer, dan jaringan kantor dari teknisi '.config('site.name').'.')

@section('content')
@include('partials.page-hero', ['eyebrow' => 'Artikel', 'title' => 'Tips & panduan', 'lead' => 'Catatan dari teknisi kami seputar perawatan perangkat kantor dan rumah.'])

<section class="b-sec">
  <div class="wrap">
    @if ($articles->isEmpty())
      <p>Belum ada artikel.</p>
    @else
      @include('articles._grid')
      @if ($articles->hasPages())
        <nav class="b-pager" aria-label="Halaman artikel">
          @if ($articles->previousPageUrl())<a href="{{ $articles->previousPageUrl() }}">← Sebelumnya</a>@endif
          <span>Halaman {{ $articles->currentPage() }} dari {{ $articles->lastPage() }}</span>
          @if ($articles->nextPageUrl())<a href="{{ $articles->nextPageUrl() }}">Berikutnya →</a>@endif
        </nav>
      @endif
    @endif
  </div>
</section>
@endsection
