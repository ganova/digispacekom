@extends('admin.layout')

@section('title', 'Pengguna & peran')
@section('heading', 'Pengguna & peran')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.users.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tambah pengguna</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px"><span><strong>Admin penuh</strong> mengelola seluruh isi situs. <strong>Staf</strong> hanya bisa menulis artikel, mengisi tiket servis, dan mengelola daftar harga/suku cadang. Menu lain tidak tampil dan alamatnya ditolak.</span></p>
<section class="card">
  <div class="tbl-wrap">
    <table class="stack">
      <thead><tr><th>Nama</th><th>Email</th><th>Peran</th><th></th></tr></thead>
      <tbody>
      @foreach ($users as $u)
        <tr>
          <td data-l="Nama"><strong>{{ $u->name }}</strong>@if ($u->is(auth()->user())) <span class="badge baru plain">Anda</span>@endif</td>
          <td data-l="Email">{{ $u->email }}</td>
          <td data-l="Peran"><span class="badge {{ $u->isAdmin() ? 'dihubungi' : 'draft' }}">{{ $u->isAdmin() ? 'Admin penuh' : 'Staf' }}</span></td>
          <td>
            <div class="row-acts">
              <a class="btn btn-sm" href="{{ route('admin.users.edit', $u) }}">Ubah</a>
              @unless ($u->is(auth()->user()))
                <form method="post" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus akses {{ $u->email }}?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
              @endunless
            </div>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection
