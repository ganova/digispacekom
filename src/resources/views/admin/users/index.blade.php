@extends('admin.layout')

@section('title', 'Daftar admin')
@section('heading', 'Daftar admin')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.users.create') }}">@include('admin.partials.icon', ['name' => 'plus', 'size' => 16]) Tambah admin</a>
@endsection

@section('content')
<section class="card">
  <div class="tbl-wrap">
    <table class="stack">
      <thead><tr><th>Nama</th><th>Email</th><th>Dibuat</th><th></th></tr></thead>
      <tbody>
      @foreach ($users as $u)
        <tr>
          <td data-l="Nama"><strong>{{ $u->name }}</strong>@if ($u->is(auth()->user())) <span class="badge baru">Anda</span>@endif</td>
          <td data-l="Email">{{ $u->email }}</td>
          <td data-l="Dibuat" class="muted">{{ $u->created_at->translatedFormat('j M Y') }}</td>
          <td>
            @unless ($u->is(auth()->user()))
              <div class="row-acts">
                <form method="post" action="{{ route('admin.users.destroy', $u) }}" data-confirm="Hapus akses {{ $u->email }}?">@csrf @method('delete')<button class="btn btn-sm btn-danger" type="submit">Hapus</button></form>
              </div>
            @endunless
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</section>
@endsection
