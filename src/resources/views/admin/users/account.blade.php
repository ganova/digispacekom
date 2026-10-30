@extends('admin.layout')

@section('title', 'Akun saya')
@section('heading', 'Akun saya')

@section('content')
<form class="card" method="post" action="{{ route('admin.account.update') }}" style="max-width:640px">
  @csrf @method('put')
  <h2>Data akun</h2>
  <p class="sub">Kosongkan bagian password bila tidak ingin menggantinya.</p>
  <div class="grid">
    <x-admin.field name="name" label="Nama" :value="$user->name" required />
    <x-admin.field name="email" label="Email login" type="email" :value="$user->email" required />
    <x-admin.field name="current_password" label="Password saat ini" type="password" autocomplete="current-password" />
    <x-admin.field name="password" label="Password baru" type="password" minlength="10" autocomplete="new-password" hint="Minimal 10 karakter." />
    <x-admin.field name="password_confirmation" label="Ulangi password baru" type="password" autocomplete="new-password" />
  </div>
  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@endsection
