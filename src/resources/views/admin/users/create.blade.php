@extends('admin.layout')

@section('title', 'Tambah admin')
@section('heading', 'Tambah admin')

@section('actions')
  <a class="btn" href="{{ route('admin.users.index') }}">← Kembali</a>
@endsection

@section('content')
<form class="card" method="post" action="{{ route('admin.users.store') }}" style="max-width:640px">
  @csrf
  <p class="note warn" style="margin-bottom:18px">Semua admin punya akses penuh ke seluruh konten. Berikan hanya kepada staf yang dipercaya.</p>
  <div class="grid">
    <x-admin.field name="name" label="Nama" required />
    <x-admin.field name="email" label="Email login" type="email" required autocomplete="off" />
    <x-admin.field name="password" label="Password" type="password" required minlength="10" autocomplete="new-password" hint="Minimal 10 karakter." />
    <x-admin.field name="password_confirmation" label="Ulangi password" type="password" required autocomplete="new-password" />
  </div>
  <div class="actions"><button class="btn btn-primary" type="submit">Tambah admin</button></div>
</form>
@endsection
