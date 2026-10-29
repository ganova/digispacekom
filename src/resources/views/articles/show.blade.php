@extends('layouts.company')

@section('title', $article->title)
@section('description', $article->excerpt)

@push('meta')
<meta property="article:published_time" content="{{ $article->published_at->toIso8601String() }}">
@endpush

@section('content')
@include('partials.page-hero', [
    'crumb' => ' / <a href="'.route('articles.index').'">Artikel</a>',
    'eyebrow' => $article->category.' · '.$article->published_at->translatedFormat('j F Y'),
    'title' => $article->title,
])

<section class="b-sec">
  <div class="wrap">
    {{-- Isi artikel ditulis admin (konten tepercaya) --}}
    <article class="b-prose">{!! $article->body !!}</article>
  </div>
</section>

@if ($more->isNotEmpty())
<section class="b-sec b-sec-alt">
  <div class="wrap">
    <h2>Artikel lainnya</h2>
    @include('articles._grid', ['articles' => $more])
  </div>
</section>
@endif
@endsection
