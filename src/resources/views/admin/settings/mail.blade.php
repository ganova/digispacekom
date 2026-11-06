@extends('admin.layout')

@section('title', 'Notifikasi email')
@section('heading', 'Notifikasi email')

@section('content')
<p class="note" style="margin-bottom:16px"><span>Setiap ada permintaan baru (booking teknisi atau minta penawaran), web mengirim email ke alamat di bawah. Kolom server SMTP adalah akun email <b>pengirim</b>. Kosongkan semuanya bila tidak ingin notifikasi.</span></p>

<form class="card" method="post" action="{{ route('admin.mail.update') }}">
  @csrf @method('put')
  <h2>Penerima</h2>
  <div class="grid">
    <x-admin.field name="notify_emails" label="Email penerima notifikasi" :rows="3" :value="implode(PHP_EOL, (array) config('site.notify_emails'))" hint="Maksimal 5 alamat. Pisahkan dengan koma atau baris baru." placeholder="admin@perusahaan.com" />
  </div>
  @error('notify_list.*')<em class="err">{{ $message }}</em>@enderror
  @error('notify_list')<em class="err">{{ $message }}</em>@enderror

  <h2 style="margin-top:28px">Akun pengirim (SMTP)</h2>
  <p class="sub">Contoh Gmail: server <code>smtp.gmail.com</code>, port <code>587</code>, username = alamat Gmail, password = <b>App Password</b> (bukan password Gmail; buat di myaccount.google.com → Keamanan → Verifikasi 2 langkah → Sandi aplikasi).</p>
  <div class="grid g2">
    <x-admin.field name="smtp_host" label="Server SMTP" :value="config('site.smtp_host')" placeholder="smtp.gmail.com" />
    <x-admin.field name="smtp_port" label="Port" type="number" :value="config('site.smtp_port') ?: 587" min="1" max="65535" hint="587 untuk TLS, 465 untuk SSL." />
    <x-admin.field name="smtp_secure" label="Enkripsi" :options="['tls' => 'TLS / STARTTLS (port 587)', 'ssl' => 'SSL (port 465)']" :value="config('site.smtp_secure') ?: 'tls'" />
    <x-admin.field name="smtp_user" label="Username" :value="config('site.smtp_user')" autocomplete="off" placeholder="alamat@gmail.com" />
    <x-admin.field name="smtp_pass" label="Password / App Password" type="password" autocomplete="new-password" :placeholder="config('site.smtp_pass') ? 'Tersimpan. Kosongkan bila tidak diganti.' : ''" hint="Disimpan terenkripsi dan tidak pernah ditampilkan lagi." />
    <x-admin.field name="mail_from" label="Email pengirim (opsional)" type="email" :value="config('site.mail_from')" hint="Kosong = sama dengan username." />
  </div>
  @if (config('site.smtp_pass'))<label class="check" style="margin-top:12px"><input type="checkbox" name="smtp_pass_clear" value="1"> Hapus password tersimpan</label>@endif

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>

<form class="card" method="post" action="{{ route('admin.mail.test') }}" style="margin-top:16px">
  @csrf
  <h2>Kirim email percobaan</h2>
  <p class="sub">Simpan pengaturan dulu, lalu kirim email percobaan ke semua penerima untuk memastikan akun pengirim berfungsi.</p>
  @error('test')<p class="flash err" role="alert">{{ $message }}</p>@enderror
  <div class="actions"><button class="btn" type="submit">Kirim email percobaan</button></div>
</form>

@if ($custom)
<form method="post" action="{{ route('admin.mail.reset') }}" data-confirm="Hapus semua pengaturan email, termasuk password tersimpan?" style="margin-top:14px">
  @csrf
  <button class="link-btn muted" type="submit">Hapus pengaturan email</button>
</form>
@endif
@endsection
