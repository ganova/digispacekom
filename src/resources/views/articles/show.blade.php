@extends('layouts.company')

@section('title', $article->title)
@section('description', $article->excerpt)

@push('meta')
@if ($article->cover)<meta property="og:image" content="{{ \App\Support\Images::url('articles', $article->cover) }}">@endif
<meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
@endpush

@section('content')
@include('partials.page-hero', [
    'crumb' => '<span>/</span><a href="'.route('articles.index').'">Artikel</a>',
    'eyebrow' => $article->category.' · '.$article->published_at->translatedFormat('j F Y'),
    'title' => $article->title,
    'lead' => $article->excerpt,
])

<section class="sec">
  <div class="wrap">
    @if ($article->cover)
      <figure class="art-hero rv"><img src="{{ \App\Support\Images::url('articles', $article->cover) }}" alt="{{ $article->title }}" decoding="async"></figure>
    @endif
    {{-- Isi artikel ditulis admin (konten tepercaya) --}}
    <article class="prose prose-article rv">{!! $article->body !!}</article>
  </div>
</section>

@if ($more->isNotEmpty())
<section class="sec sec-soft">
  <div class="wrap">
    <div class="sec-hd rv"><span class="eyebrow">Baca juga</span><h2>Artikel lainnya</h2></div>
    @include('articles._grid', ['articles' => $more])
  </div>
</section>
@endif
@endsection
