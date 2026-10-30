<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Masuk · CMS {{ config('site.short') }}</title>
<link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,600..800&family=Geist:wght@400..700&display=swap">
<link rel="stylesheet" href="{{ \App\Support\Site::asset('css/admin.css') }}">
</head>
<body class="login">
<span class="orb o1"></span><span class="orb o2"></span><span class="orb o3"></span><span class="mesh"></span>

<main class="login-card">
  <div class="brand" style="--i:0"><span class="logo-mark">DS</span>{{ config('site.short') }}</div>
  <h1 style="--i:1">Selamat datang <em>kembali</em>.</h1>
  <p class="lead" style="--i:2">Masuk untuk mengelola konten, harga, dan tiket servis.</p>

  @if ($errors->any())<p class="flash err" role="alert">{{ $errors->first() }}</p>@endif

  <form method="post" action="{{ route('admin.login.store') }}" class="grid" id="login-form" style="--i:3">
    @csrf
    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <div class="pw">
        <input id="password" type="password" name="password" autocomplete="current-password" required>
        <button type="button" id="pw-toggle" aria-label="Tampilkan password" aria-pressed="false">
          <span class="show">@include('admin.partials.icon', ['name' => 'eye', 'size' => 18])</span>
          <span class="hide" hidden>@include('admin.partials.icon', ['name' => 'eye-off', 'size' => 18])</span>
        </button>
      </div>
      <span class="caps" id="caps">Caps Lock sedang aktif.</span>
    </div>
    <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini</label>
    <button class="btn btn-primary btn-block" type="submit">Masuk @include('admin.partials.icon', ['name' => 'arrow', 'size' => 17])</button>
  </form>
  <p class="foot" style="--i:4">Halaman khusus admin {{ config('site.name') }}</p>
</main>

<script>
  (() => {
    const pw = document.getElementById('password'), t = document.getElementById('pw-toggle'), caps = document.getElementById('caps');
    t.addEventListener('click', () => {
      const show = pw.type === 'password';
      pw.type = show ? 'text' : 'password';
      t.setAttribute('aria-pressed', show);
      t.querySelector('.show').hidden = show;
      t.querySelector('.hide').hidden = !show;
      pw.focus();
    });
    ['keydown', 'keyup'].forEach(ev => pw.addEventListener(ev, e => caps.classList.toggle('on', e.getModifierState && e.getModifierState('CapsLock'))));
    document.getElementById('login-form').addEventListener('submit', e => e.target.querySelector('.btn-primary').classList.add('is-loading'));
  })();
</script>
</body>
</html>
