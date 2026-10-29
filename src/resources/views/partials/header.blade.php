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
    <a class="btn btn-pri btn-sm hd-cta" href="{{ $cta[1] }}" data-magnetic>{{ $cta[0] }}<span class="arw" aria-hidden="true">→</span></a>
    <button class="hd-tg" type="button" aria-expanded="false" aria-controls="hd-nav"><i></i><i></i><span class="sr-only">Menu</span></button>
  </div>
</header>
