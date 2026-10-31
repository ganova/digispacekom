@php($categories = \App\Models\PriceItem::categories())
<p class="c-count mono">{{ $items->count() }} estimasi{{ $q !== '' ? ' untuk "'.$q.'"' : '' }}</p>
@forelse ($items as $item)
  @php($catLabel = $categories[$item->category] ?? ucfirst($item->category))
  <div @class(['c-row rv in', 'has-img' => $item->image]) style="--d:{{ min($loop->index, 8) }}">
    @if ($item->image)<img class="c-thumb" src="{{ \App\Support\Images::url('prices', $item->image) }}" alt="{{ $item->name }}" loading="lazy" decoding="async" width="72" height="72">@endif
    <span class="c-cat">{{ $catLabel }}</span>
    <b>{{ $item->name }}</b>
    <strong class="mono">{{ $item->priceLabel() }}</strong>
    <small>{{ $item->note }}@if ($item->stock) <em class="c-stock {{ $item->stock }}">{{ \App\Models\PriceItem::STOCK[$item->stock] }}</em>@endif</small>
    <a href="{{ route('landing', 'servis-panggilan') }}?{{ http_build_query(['perangkat' => $catLabel, 'keluhan' => $item->name]) }}#booking">Booking →</a>
  </div>
@empty
  <div class="c-empty">
    <b>Belum ada estimasi untuk "{{ $q }}".</b>
    <p>Teknisi kami bisa memberi perkiraan langsung.</p>
    <a class="btn btn-pri btn-sm" href="{{ \App\Support\Site::whatsappUrl('Halo, saya mau tanya biaya service: '.$q) }}" target="_blank" rel="noopener">Tanya lewat WhatsApp</a>
  </div>
@endforelse
