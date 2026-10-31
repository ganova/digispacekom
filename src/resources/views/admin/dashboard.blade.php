@extends('admin.layout')

@section('title', 'Dasbor')
@section('heading', 'Dasbor')
@section('no_head', true)

@php
    $hour = (int) now()->format('G');
    $greet = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));
    $first = explode(' ', auth()->user()->name)[0];
    $max = max(1, collect($chart)->max('count'));
    $total = collect($chart)->sum('count');
    $done = collect($todo)->where('done', true)->count();
    $pct = (int) round($done / max(1, count($todo)) * 100);
    $statuses = \App\Models\Booking::STATUSES;
    $isAdmin = auth()->user()->isAdmin();
@endphp

@section('content')
<section class="hero">
  <div class="grid-bg"></div>
  <div>
    <p class="eyebrow" style="color:#A9B6DD">{{ now()->translatedFormat('l, j F Y') }}</p>
    <h1>{{ $greet }}, <em>{{ $first }}</em>.</h1>
    <p>
      @if (! $isAdmin)
        Siap mengisi tiket servis, daftar harga, dan artikel hari ini?
      @elseif ($newBookings)
        Ada <strong style="color:#fff">{{ $newBookings }} permintaan baru</strong> yang menunggu dihubungi.
      @else
        Semua permintaan sudah ditindaklanjuti. Kerja bagus.
      @endif
    </p>
  </div>
  <div class="acts">
    <a class="btn btn-lime" href="{{ route('admin.tickets.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tiket baru</a>
    @if ($isAdmin)<a class="btn" href="{{ route('admin.bookings.index', ['status' => 'baru']) }}">Lihat permintaan</a>@else<a class="btn" href="{{ route('admin.articles.create') }}">Tulis artikel</a>@endif
  </div>
</section>

<div class="stats">
  @if ($isAdmin)
  <a class="stat {{ $newBookings ? 'hot' : '' }}" style="--j:0" href="{{ route('admin.bookings.index', ['status' => 'baru']) }}">
    <span class="ic">@include('admin.partials.icon', ['name' => 'inbox'])</span>
    <div><b data-count="{{ $newBookings }}">{{ $newBookings }}</b><span>Permintaan baru</span></div>
    <span class="go">@include('admin.partials.icon', ['name' => 'arrow', 'size' => 16])</span>
  </a>
  @endif
  <a class="stat" style="--j:1" href="{{ route('admin.tickets.index') }}">
    <span class="ic">@include('admin.partials.icon', ['name' => 'ticket'])</span>
    <div><b data-count="{{ $activeTickets }}">{{ $activeTickets }}</b><span>Servis berjalan · {{ $tickets }} tiket</span></div>
    <span class="go">@include('admin.partials.icon', ['name' => 'arrow', 'size' => 16])</span>
  </a>
  <a class="stat" style="--j:2" href="{{ route('admin.prices.index') }}">
    <span class="ic">@include('admin.partials.icon', ['name' => 'tag'])</span>
    <div><b data-count="{{ $prices }}">{{ $prices }}</b><span>Item daftar harga</span></div>
    <span class="go">@include('admin.partials.icon', ['name' => 'arrow', 'size' => 16])</span>
  </a>
  <a class="stat" style="--j:3" href="{{ route('admin.articles.index') }}">
    <span class="ic">@include('admin.partials.icon', ['name' => 'doc'])</span>
    <div><b data-count="{{ $articles }}">{{ $articles }}</b><span>Artikel{{ $drafts ? " · {$drafts} draf" : '' }}</span></div>
    <span class="go">@include('admin.partials.icon', ['name' => 'arrow', 'size' => 16])</span>
  </a>
</div>

<div class="two">
  <div class="col">
    @if ($isAdmin)
    <section class="card">
      <div class="card-head">
        <div><h2>Permintaan 14 hari terakhir</h2><p class="sub">Isian form booking dan minta penawaran per hari.</p></div>
        <div class="legend"><strong data-count="{{ $total }}">{{ $total }}</strong><span class="muted">total · {{ $weekBookings }} minggu ini</span></div>
      </div>
      <div class="chart" style="--n:{{ count($chart) }}" role="img" aria-label="Grafik permintaan per hari, total {{ $total }}">
        @foreach ($chart as $k => $d)
          <div @class(['bar', 'today' => $d['today'], 'zero' => ! $d['count']]) style="--h:{{ $d['count'] ? max(4, round($d['count'] / $max * 100)) : 2 }};--k:{{ $k }}">
            <b>{{ $d['count'] }} · {{ $d['label'] }}</b><i></i>
          </div>
        @endforeach
      </div>
      <div class="chart-x" style="--n:{{ count($chart) }}">
        @foreach ($chart as $k => $d)<span @class(['now' => $d['today']])>{{ $d['today'] ? 'Ini' : $d['day'] }}</span>@endforeach
      </div>
    </section>

    <section class="card">
      <div class="card-head">
        <div><h2>Permintaan terbaru</h2><p class="sub">Klik untuk melihat detail dan menindaklanjuti.</p></div>
        <a class="btn btn-sm" href="{{ route('admin.bookings.index') }}">Semua</a>
      </div>
      @if ($latest->isEmpty())
        <p class="empty">Belum ada permintaan masuk.</p>
      @else
        <ul class="feed">
          @foreach ($latest as $i => $b)
            @php $who = $b->name ?: $b->organization ?: 'Tanpa nama'; @endphp
            <li style="--r:{{ $i }}">
              <a href="{{ route('admin.bookings.show', $b) }}">
                <span class="dot">{{ mb_strtoupper(mb_substr($who, 0, 1)) }}</span>
                <span class="body"><strong>{{ $who }}</strong><span>{{ $b->device ? $b->device.' — ' : '' }}{{ $b->complaint }}</span></span>
                <span style="text-align:right"><span class="badge {{ $b->status }}">{{ $statuses[$b->status] ?? $b->status }}</span><br><time datetime="{{ $b->created_at->toIso8601String() }}">{{ $b->created_at->diffForHumans() }}</time></span>
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </section>
    @else
    @include('admin.partials.staff-shortcuts')
    @endif
  </div>

  <div class="col">
    @if ($isAdmin)
    <section class="card">
      <div class="card-head">
        <div><h2>Siap tayang</h2><p class="sub">Konten contoh yang perlu diganti sebelum situs diumumkan.</p></div>
        <div class="ring" style="--p:{{ $pct }}"><span>{{ $pct }}%</span></div>
      </div>
      <ul class="todo">
        @foreach ($todo as $t)
          <li @class(['done' => $t['done']])>
            <a href="{{ $t['url'] }}">
              <span class="tick">@if ($t['done'])@include('admin.partials.icon', ['name' => 'check', 'size' => 13])@endif</span>
              <span class="txt">{{ $t['text'] }}<small>{{ $t['hint'] }}</small></span>
              @include('admin.partials.icon', ['name' => 'arrow', 'size' => 15])
            </a>
          </li>
        @endforeach
      </ul>
    </section>
    @endif

    <section class="card">
      <div class="card-head">
        <div><h2>Servis berjalan</h2><p class="sub">Tiket dengan tahap yang sedang dikerjakan.</p></div>
        <a class="btn btn-sm" href="{{ route('admin.tickets.index') }}">Semua</a>
      </div>
      @if ($inProgress->isEmpty())
        <p class="empty">Tidak ada servis yang sedang berjalan.</p>
      @else
        <ul class="feed">
          @foreach ($inProgress as $i => $t)
            <li style="--r:{{ $i }}">
              <a href="{{ route('admin.tickets.edit', $t) }}">
                <span class="dot">@include('admin.partials.icon', ['name' => 'ticket', 'size' => 17])</span>
                <span class="body"><strong class="mono">{{ $t->code }}</strong><span>{{ $t->device }}</span></span>
                <span class="badge now">{{ $t->events->first()?->title }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      @endif
    </section>
  </div>
</div>
@endsection
