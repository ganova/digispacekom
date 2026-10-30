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
<main class="card">
  <div class="brand"><b>DS</b>{{ config('site.short') }}</div>
  <h1>Masuk ke CMS</h1>
  <p class="muted" style="margin:0 0 20px">Kelola konten, harga, dan tiket servis.</p>

  @if ($errors->any())<p class="flash err" role="alert">{{ $errors->first() }}</p>@endif

  <form method="post" action="{{ route('admin.login.store') }}" class="grid">
    @csrf
    <div class="field">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input id="password" type="password" name="password" autocomplete="current-password" required>
    </div>
    <label class="check"><input type="checkbox" name="remember" value="1"> Ingat saya di perangkat ini</label>
    <button class="btn btn-primary btn-block" type="submit">Masuk</button>
  </form>
</main>
</body>
</html>
