@extends('layouts.company')

@section('title', 'Artikel')
@section('description', 'Tips merawat laptop, printer, dan jaringan kantor dari teknisi '.config('site.name').'.')

@section('content')
@include('partials.page-hero', [
    'eyebrow' => 'Artikel',
    'titleParts' => [['Tips &'], ['panduan.', 'grad']],
    'lead' => 'Catatan dari teknisi kami seputar perawatan perangkat kantor dan rumah.',
])

<section class="sec">
  <div class="wrap">
    @if ($articles->isEmpty())
      <p>Belum ada artikel.</p>
    @else
      @include('articles._grid')
      @if ($articles->hasPages())
        <nav class="pager" aria-label="Halaman artikel">
          @if ($articles->previousPageUrl())<a class="btn btn-ghost btn-sm" href="{{ $articles->previousPageUrl() }}">← Sebelumnya</a>@endif
          <span>Halaman {{ $articles->currentPage() }} dari {{ $articles->lastPage() }}</span>
          @if ($articles->nextPageUrl())<a class="btn btn-ghost btn-sm" href="{{ $articles->nextPageUrl() }}">Berikutnya →</a>@endif
        </nav>
      @endif
    @endif
  </div>
</section>
@endsection
