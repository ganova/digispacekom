@php([$wmMain, $wmSmall] = \App\Support\Site::wordmark())
@php($logo = config('site.logo'))
@if ($logo)
  {{-- Logo dari CMS: versi terang untuk latar terang; versi gelap (atau logo terang di kotak putih) untuk latar gelap --}}
  <img class="lg-img lg-l" src="{{ \App\Support\Images::url('brand', $logo) }}" alt="{{ config('site.logo_show_name') ? '' : config('site.name') }}" decoding="async">
  @if (config('site.logo_dark'))
    <img class="lg-img lg-d" src="{{ \App\Support\Images::url('brand', config('site.logo_dark')) }}" alt="" aria-hidden="true" decoding="async">
  @else
    <span class="lg-d lg-chip" aria-hidden="true"><img class="lg-img" src="{{ \App\Support\Images::url('brand', $logo) }}" alt="" decoding="async"></span>
  @endif
  @if (config('site.logo_show_name'))
    <span class="lg-name"><b>{{ $wmMain }}</b>@if ($wmSmall)<small>{{ $wmSmall }}</small>@endif</span>
  @endif
@else
  {{-- Logo sementara (tiruan). Unggah logo asli di CMS: Profil & kontak. --}}
  <span class="mk" aria-hidden="true"></span><span><b>{{ $wmMain }}</b>@if ($wmSmall)<small>{{ $wmSmall }}</small>@endif</span>
@endif
