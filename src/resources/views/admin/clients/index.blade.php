@extends('admin.layout')

@section('title', 'Klien & logo')
@section('heading', 'Klien & logo')

@section('actions')
  <a class="btn" href="{{ route('about') }}#klien" target="_blank" rel="noopener">Lihat di situs</a>
@endsection

@section('content')
<p class="note" style="margin-bottom:16px"><span>Tampil di beranda (pita bergerak), landing page iklan, dan halaman Tentang. <strong>Logo opsional</strong>: bila tidak ada, nama klien yang ditampilkan. Gunakan klien yang sudah setuju namanya dan logonya dipublikasikan.</span></p>

<form method="post" action="{{ route('admin.clients.update') }}" enctype="multipart/form-data" class="card">
  @csrf @method('put')
  <div class="card-head">
    <div><h2>Daftar klien</h2><p class="sub">Urutan di sini adalah urutan tampil. Logo PNG, JPG, atau WebP, maksimal 2 MB. Latar transparan paling bagus.</p></div>
  </div>

  <div class="events" id="clients">
    @foreach ($clients as $i => $c)
      <div class="ev cl-row" data-state="next">
        <span class="n">{{ $i + 1 }}</span>
        <input name="clients[{{ $i }}][name]" value="{{ $c['name'] }}" placeholder="Nama klien" aria-label="Nama klien" maxlength="80">
        <div class="cl-logo">
          <span class="cl-prev">@if ($c['logo_url'])<img src="{{ $c['logo_url'] }}" alt="Logo {{ $c['name'] }}">@else<em>Tanpa logo</em>@endif</span>
          <label class="btn btn-sm cl-pick"><input type="file" name="clients[{{ $i }}][logo]" accept="image/png,image/jpeg,image/webp" hidden>Unggah logo</label>
          @if ($c['logo'])<label class="check cl-rm"><input type="checkbox" name="clients[{{ $i }}][logo_remove]" value="1"> Hapus logo</label>@endif
          <input type="hidden" name="clients[{{ $i }}][logo_current]" value="{{ $c['logo'] }}">
        </div>
        <div class="mv">
          <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
          <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
          <button type="button" data-del title="Hapus klien" aria-label="Hapus klien">✕</button>
        </div>
      </div>
    @endforeach
  </div>
  <button type="button" class="add-ev" id="add-client">+ Tambah klien</button>

  <template id="cl-tpl">
    <div class="ev cl-row" data-state="next">
      <span class="n"></span>
      <input name="clients[__][name]" placeholder="Nama klien" aria-label="Nama klien" maxlength="80">
      <div class="cl-logo">
        <span class="cl-prev"><em>Tanpa logo</em></span>
        <label class="btn btn-sm cl-pick"><input type="file" name="clients[__][logo]" accept="image/png,image/jpeg,image/webp" hidden>Unggah logo</label>
        <input type="hidden" name="clients[__][logo_current]" value="">
      </div>
      <div class="mv">
        <button type="button" data-up title="Naikkan" aria-label="Naikkan">↑</button>
        <button type="button" data-down title="Turunkan" aria-label="Turunkan">↓</button>
        <button type="button" data-del title="Hapus klien" aria-label="Hapus klien">✕</button>
      </div>
    </div>
  </template>

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'klien', 'custom' => $custom])
@endsection
