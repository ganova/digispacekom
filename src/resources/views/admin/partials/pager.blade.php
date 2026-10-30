@if ($paginator->hasPages())
<nav class="pager" aria-label="Halaman">
  @if ($paginator->onFirstPage())<span class="dis">‹</span>@else<a href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>@endif
  <span class="on">{{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
  @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>@else<span class="dis">›</span>@endif
</nav>
@endif
