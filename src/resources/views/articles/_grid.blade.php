<div class="b-arts">
  @foreach ($articles as $article)
    <a class="b-art" href="{{ route('articles.show', $article) }}">
      <small>{{ $article->category }} · {{ $article->published_at->translatedFormat('j M Y') }}</small>
      <b>{{ $article->title }}</b>
      <span>{{ $article->excerpt }}</span>
    </a>
  @endforeach
</div>
