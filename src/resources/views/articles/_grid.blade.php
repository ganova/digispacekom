<div class="arts">
  @foreach ($articles as $article)
    <a class="art spot rv" style="--d:{{ $loop->index }}" href="{{ route('articles.show', $article) }}">
      <div class="art-cover" aria-hidden="true" style="--h:{{ (crc32($article->slug) % 360) }}"><span>{{ $article->category }}</span></div>
      <small class="mono">{{ $article->published_at->translatedFormat('j M Y') }}</small>
      <b>{{ $article->title }}</b>
      <span>{{ $article->excerpt }}</span>
      <i class="link">Baca artikel →</i>
    </a>
  @endforeach
</div>
