{{-- Header bersama: transparan di atas hero gelap, berubah jadi kaca saat digulir --}}
<header class="hd" id="top">
  <div class="wrap hd-in">
    <a href="{{ route('home') }}" class="lg" aria-label="{{ config('site.name') }} — Beranda">@include('partials.logo')</a>
    <nav class="hd-nav" id="hd-nav" aria-label="Menu utama">
      <span class="hd-pill" aria-hidden="true"></span>
      @foreach ($links as [$label, $url, $active])
        <a href="{{ $url }}" @class(['on' => $active]) style="--i:{{ $loop->index }}">{{ $label }}</a>
      @endforeach
      <a class="btn btn-pri hd-mcta" href="{{ $cta[1] }}" style="--i:{{ count($links) }}">{{ $cta[0] }}</a>
    </nav>
    <button class="th-tg" type="button" id="theme-tg" aria-label="Ganti tema tampilan" title="Tema tampilan">
      <svg class="i-auto" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 3v18" /><path d="M12 3a9 9 0 0 1 0 18z" fill="currentColor" stroke="none"/></svg>
      <svg class="i-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
      <svg class="i-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg>
      <span class="tip" id="theme-tip" aria-live="polite"></span>
    </button>
    <a class="btn btn-pri btn-sm hd-cta" href="{{ $cta[1] }}" data-magnetic>{{ $cta[0] }}<span class="arw" aria-hidden="true">→</span></a>
    <button class="hd-tg" type="button" aria-expanded="false" aria-controls="hd-nav"><i></i><i></i><span class="sr-only">Menu</span></button>
  </div>
</header>
