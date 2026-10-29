<section class="b-page">
  <div class="wrap">
    @isset($crumb)<nav class="b-crumb" aria-label="Breadcrumb"><a href="{{ route('home') }}">Beranda</a>{!! $crumb !!}</nav>@endisset
    @isset($eyebrow)<span class="b-ey">{{ $eyebrow }}</span>@endisset
    <h1>{{ $title }}</h1>
    @isset($lead)<p>{{ $lead }}</p>@endisset
  </div>
</section>
