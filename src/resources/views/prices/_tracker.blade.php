@php($events = $ticket?->events ?? collect())
@php($doneCount = $events->where('state', 'done')->count() + ($events->contains('state', 'now') ? .5 : 0))
<div class="c-trk rv">
  <div class="c-trk-l">
    <span class="eyebrow eyebrow-inv">Cek status servis</span>
    <h2>Sudah sampai mana servisnya?</h2>
    <p>Masukkan nomor tiket dari tanda terima servis.</p>
    <form class="tk" method="get" action="{{ route('status') }}">
      <label for="tiket" class="sr-only">Nomor tiket</label>
      <input id="tiket" class="mono" name="tiket" value="{{ $code }}" placeholder="DSK-1028" required autocomplete="off" autocapitalize="characters">
      <button type="submit" class="btn btn-lime btn-sm">Cek</button>
    </form>
  </div>

  @if ($ticket)
    <div class="c-res">
      <div class="c-meta">
        <span class="mono c-code">{{ $ticket->code }}</span>
        <span>{{ $ticket->device }}</span>
        <span>a.n. {{ $ticket->maskedName() }}</span>
      </div>
      <ol class="c-tl" style="--prog:{{ $events->count() > 1 ? round(($doneCount - .5) / ($events->count() - 1), 3) : 0 }}">
        @foreach ($events as $event)
          <li class="{{ $event->state }}" style="--d:{{ $loop->index }}">
            <i aria-hidden="true">@if ($event->state === 'done')✓@endif</i>
            <b>{{ $event->title }}</b>
            <span>{{ $event->occurred_at?->translatedFormat('D, j M · H.i') ?? $event->note ?? '–' }}</span>
            @if ($event->occurred_at && $event->note)<small>{{ $event->note }}</small>@endif
          </li>
        @endforeach
      </ol>
    </div>
  @elseif ($code !== '')
    <div class="c-res c-miss"><b>Tiket {{ $code }} tidak ditemukan.</b><p>Periksa kembali nomornya atau <a href="{{ \App\Support\Site::whatsappUrl('Halo, saya mau cek status servis tiket '.$code) }}" target="_blank" rel="noopener">tanya lewat WhatsApp</a>.</p></div>
  @else
    <div class="c-res c-miss c-demo" aria-hidden="{{ empty($compact) ? 'false' : 'true' }}">
      <ol class="c-tl c-tl-demo" style="--prog:.5">
        <li class="done"><i>✓</i><b>Diterima</b><span>Tanda terima &amp; tiket</span></li>
        <li class="done"><i>✓</i><b>Dicek teknisi</b><span>Diagnosa &amp; estimasi</span></li>
        <li class="now"><i></i><b>Dikerjakan</b><span>Perbaikan berjalan</span></li>
        <li class="next"><i></i><b>Siap diambil</b><span>Notifikasi WA</span></li>
      </ol>
      @if (empty($compact))<p>Coba tiket contoh <a href="{{ route('status', ['tiket' => 'DSK-1028']) }}">DSK-1028</a>.</p>@endif
    </div>
  @endif
</div>
