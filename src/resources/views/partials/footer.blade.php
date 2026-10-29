<footer class="ft {{ $class ?? '' }}">
  <div class="wrap ft-in">
    <div>
      <a href="{{ route('home') }}" class="lg inv" aria-label="Beranda">@include('partials.logo')</a>
      <p>{{ config('site.name') }} · {{ config('site.address') }}<br>{{ config('site.hours') }}</p>
    </div>
    <div>
      <h2>Layanan</h2>
      <ul>
        @foreach (config('site.services') as $slug => $s)
          <li><a href="{{ route('services.show', $slug) }}">{{ $s['title'] }}</a></li>
        @endforeach
      </ul>
    </div>
    <div>
      <h2>Perusahaan</h2>
      <ul>
        <li><a href="{{ route('about') }}">Tentang &amp; Klien</a></li>
        <li><a href="{{ route('articles.index') }}">Artikel</a></li>
        <li><a href="{{ route('prices.index') }}">Cek Harga</a></li>
        <li><a href="{{ route('status') }}">Cek Status Servis</a></li>
        <li><a href="{{ route('contact') }}">Kontak</a></li>
      </ul>
    </div>
    <div>
      <h2>Kontak</h2>
      <ul>
        <li><a href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener">WA {{ config('site.phone') }}</a></li>
        <li><a href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></li>
        <li>Area: {{ config('site.area') }}</li>
      </ul>
    </div>
  </div>
  <div class="wrap ft-bt">© {{ date('Y') }} {{ config('site.name') }}</div>
</footer>
