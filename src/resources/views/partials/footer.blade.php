<footer class="ft">
  <div class="wrap">
    <div class="ft-cta rv">
      <div>
        <span class="eyebrow eyebrow-inv">Siap membantu hari ini</span>
        <h2>Perangkat bermasalah? <span class="grad">Teknisi kami siap datang.</span></h2>
      </div>
      <div class="ft-cta-btns">
        <a class="btn btn-lime" href="{{ \App\Support\Site::whatsappUrl() }}" target="_blank" rel="noopener" data-magnetic>Chat {{ config('site.phone') }}<span class="arw" aria-hidden="true">→</span></a>
        <a class="btn btn-ghost-inv" href="{{ route('contact') }}#penawaran">Minta penawaran</a>
      </div>
    </div>

    <div class="ft-in">
      <div class="ft-brand">
        <a href="{{ route('home') }}" class="lg inv" aria-label="Beranda">@include('partials.logo')</a>
        <p>{{ config('site.tagline') }}. Melayani seluruh {{ config('site.area') }}.</p>
        <p class="ft-open"><span class="dot-live" aria-hidden="true"></span>{{ config('site.hours') }}</p>
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
          <li>{{ config('site.address') }}</li>
        </ul>
      </div>
    </div>
  </div>
  <div class="ft-word" aria-hidden="true">DIGITAL SPACE</div>
  <div class="wrap ft-bt">
    <span>© {{ date('Y') }} {{ config('site.name') }}</span>
    <a href="#top">Kembali ke atas ↑</a>
  </div>
</footer>
