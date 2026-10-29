<section class="ph">
  <div class="aurora" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="wrap">
    @isset($crumb)<nav class="crumb fade-up" style="--fd:.05s" aria-label="Breadcrumb"><a href="{{ route('home') }}">Beranda</a>{!! $crumb !!}</nav>@endisset
    @isset($eyebrow)<span class="eyebrow eyebrow-inv fade-up" style="--fd:.1s">{{ $eyebrow }}</span>@endisset
    <h1 class="split">{!! \App\Support\Site::split($titleParts ?? [[$title]]) !!}</h1>
    @isset($lead)<p class="fade-up" style="--fd:.45s">{{ $lead }}</p>@endisset
    @isset($actions)<div class="ph-act fade-up" style="--fd:.6s">{!! $actions !!}</div>@endisset
  </div>
</section>
