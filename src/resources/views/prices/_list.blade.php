@php($categories = \App\Models\PriceItem::CATEGORIES)
<p class="c-count mono">{{ $items->count() }} estimasi{{ $q !== '' ? ' untuk "'.$q.'"' : '' }}</p>
@forelse ($items as $item)
  <div class="c-row rv in" style="--d:{{ min($loop->index, 8) }}">
    <span class="c-cat">{{ $categories[$item->category] }}</span>
    <b>{{ $item->name }}</b>
    <strong class="mono">{{ $item->priceLabel() }}</strong>
    <small>{{ $item->note }}</small>
    <a href="{{ route('landing', 'servis-panggilan') }}?{{ http_build_query(['perangkat' => $categories[$item->category], 'keluhan' => $item->name]) }}#booking">Booking →</a>
  </div>
@empty
  <div class="c-empty">
    <b>Belum ada estimasi untuk "{{ $q }}".</b>
    <p>Teknisi kami bisa memberi perkiraan langsung.</p>
    <a class="btn btn-pri btn-sm" href="{{ \App\Support\Site::whatsappUrl('Halo, saya mau tanya biaya service: '.$q) }}" target="_blank" rel="noopener">Tanya lewat WhatsApp</a>
  </div>
@endforelse
