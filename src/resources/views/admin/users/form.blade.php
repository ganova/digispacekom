@extends('admin.layout')

@php $editing = $user->exists; @endphp
@section('title', $editing ? 'Ubah pengguna' : 'Tambah pengguna')
@section('heading', $editing ? 'Ubah pengguna' : 'Tambah pengguna')

@section('actions')
  <a class="btn" href="{{ route('admin.users.index') }}">← Kembali</a>
@endsection

@section('content')
<form class="card" method="post" action="{{ $editing ? route('admin.users.update', $user) : route('admin.users.store') }}" style="max-width:680px">
  @csrf
  @if ($editing) @method('put') @endif

  <div class="grid">
    <x-admin.field name="name" label="Nama" :value="$user->name" required />
    <x-admin.field name="email" label="Email login" type="email" :value="$user->email" required autocomplete="off" />

    <div class="field">
      <label>Peran<i>*</i></label>
      <div class="sel-list" style="grid-template-columns:1fr">
        @foreach (\App\Models\User::ROLES as $key => $label)
          <label>
            <input type="radio" name="role" value="{{ $key }}" @checked(old('role', $user->role) === $key) @disabled($editing && $user->is(auth()->user()))>
            <span>
              <strong>{{ $key === 'admin' ? 'Admin penuh' : 'Staf' }}</strong><br>
              <small class="muted">{{ $key === 'admin' ? 'Mengelola seluruh isi situs, permintaan masuk, dan pengguna.' : 'Hanya artikel, tiket servis, dan daftar harga/suku cadang.' }}</small>
            </span>
          </label>
        @endforeach
      </div>
      @if ($editing && $user->is(auth()->user()))
        <input type="hidden" name="role" value="{{ $user->role }}">
        <small>Anda tidak bisa mengubah peran akun sendiri.</small>
      @endif
      @error('role')<em class="err">{{ $message }}</em>@enderror
    </div>

    <x-admin.field name="password" :label="$editing ? 'Password baru' : 'Password'" type="password" :required="! $editing" minlength="10" autocomplete="new-password" :hint="$editing ? 'Kosongkan bila tidak ingin mengganti. Minimal 10 karakter.' : 'Minimal 10 karakter.'" />
    <x-admin.field name="password_confirmation" label="Ulangi password" type="password" :required="! $editing" autocomplete="new-password" />
  </div>

  <div class="actions"><button class="btn btn-primary" type="submit">{{ $editing ? 'Simpan' : 'Tambah pengguna' }}</button></div>
</form>
@endsection
