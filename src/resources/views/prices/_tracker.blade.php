<div class="c-trk">
  <div>
    <h2>Cek status servis</h2>
    <p>Masukkan nomor tiket dari tanda terima servis.</p>
    <form class="tk" method="get" action="{{ route('status') }}">
      <label for="tiket" class="sr-only">Nomor tiket</label>
      <input id="tiket" name="tiket" value="{{ $code }}" placeholder="Contoh: DSK-1028" required autocomplete="off" autocapitalize="characters">
      <button type="submit">Cek</button>
    </form>
  </div>

  @if ($ticket)
    <div class="c-res">
      <p class="c-meta"><b>{{ $ticket->code }}</b> · {{ $ticket->device }} · a.n. {{ $ticket->maskedName() }}</p>
      <ol class="c-tl">
        @foreach ($ticket->events as $event)
          <li class="{{ $event->state }}">
            <b>{{ $event->title }}</b>
            {{ $event->occurred_at?->translatedFormat('D, j M · H.i') ?? $event->note ?? '–' }}
            @if ($event->occurred_at && $event->note)<small>{{ $event->note }}</small>@endif
          </li>
        @endforeach
      </ol>
    </div>
  @elseif ($code !== '')
    <p class="c-res c-miss">Tiket <b>{{ $code }}</b> tidak ditemukan. Periksa kembali nomornya atau <a href="{{ \App\Support\Site::whatsappUrl('Halo, saya mau cek status servis tiket '.$code) }}" target="_blank" rel="noopener">tanya lewat WhatsApp</a>.</p>
  @elseif (empty($compact))
    <p class="c-res c-miss">Coba tiket contoh <a href="{{ route('status', ['tiket' => 'DSK-1028']) }}">DSK-1028</a>.</p>
  @endif
</div>
