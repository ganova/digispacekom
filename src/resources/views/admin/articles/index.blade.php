@extends('admin.layout')

@section('title', 'Artikel')
@section('heading', 'Artikel')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.articles.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tulis artikel</a>
@endsection

@section('content')
<section class="card">
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul artikel…">
    <button class="btn" type="submit">Cari</button>
  </form>

  @if ($articles->isEmpty())
    <p class="empty">Belum ada artikel.</p>
  @else
    <div class="tbl-wrap">
      <table class="stack">
        <thead><tr><th>Judul</th><th>Kategori</th><th>Status</th><th></th></tr></thead>
        <tbody>
        @foreach ($articles as $a)
          @php $live = $a->published_at && $a->published_at->isPast(); @endphp
          <tr>
            <td data-l="Judul"><a class="t" href="{{ route('admin.articles.edit', $a) }}">{{ $a->title }}</a><span class="sub mono">/artikel/{{ $a->slug }}</span></td>
            <td data-l="Kategori">{{ $a->category ?: '—' }}</td>
            <td data-l="Status">
              @if ($live)<span class="badge selesai">Terbit {{ $a->published_at->translatedFormat('j M Y') }}</span>
              @elseif ($a->published_at)<span class="badge dihubungi">Terjadwal {{ $a->published_at->translatedFormat('j M Y H:i') }}</span>
              @else<span class="badge draft">Draf</span>@endif
            </td>
            <td>
              <div class="row-acts">
                @if ($live)<a class="btn btn-sm" href="{{ route('articles.show', $a) }}" target="_blank" rel="noopener">Lihat</a>@endif
                <a class="btn btn-sm" href="{{ route('admin.articles.edit', $a) }}">Ubah</a>
                <form method="post" action="{{ route('admin.articles.destroy', $a) }}" data-confirm="Hapus artikel “{{ $a->title }}”?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
              </div>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    @include('admin.partials.pager', ['paginator' => $articles])
  @endif
</section>
@endsection
