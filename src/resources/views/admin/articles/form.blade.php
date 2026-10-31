@extends('admin.layout')

@php
    $editing = $article->exists;
    $mode = old('publish', ! $editing ? 'now' : ($article->published_at ? ($article->published_at->isPast() ? 'now' : 'scheduled') : 'draft'));
@endphp
@section('title', $editing ? 'Ubah artikel' : 'Tulis artikel')
@section('heading', $editing ? 'Ubah artikel' : 'Tulis artikel')

@section('actions')
  <a class="btn" href="{{ route('admin.articles.index') }}">← Kembali</a>
@endsection

@section('content')
<form class="card" method="post" enctype="multipart/form-data" data-upload="{{ route('admin.upload') }}" action="{{ $editing ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
  @csrf
  @if ($editing) @method('put') @endif

  <div class="grid g2">
    <x-admin.field name="title" label="Judul" :value="$article->title" required class="span2 field" />
    <x-admin.field name="category" label="Kategori" :value="$article->category" placeholder="Tips, Jaringan, Berita…" />
    <x-admin.field name="slug" label="Alamat (slug)" :value="$article->slug" hint="Kosongkan agar dibuat otomatis dari judul." />
    <x-admin.field name="excerpt" label="Ringkasan" :rows="3" :value="$article->excerpt" required class="span2 field" hint="Maksimal 300 karakter. Tampil di daftar artikel dan hasil pencarian Google." maxlength="300" />

    <div class="field span2">
      <label for="f-cover_file">Gambar sampul (opsional)</label>
      <div class="img-pick">
        <span class="img-prev img-wide" id="cover-prev">@if ($article->cover)<img src="{{ \App\Support\Images::url('articles', $article->cover) }}" alt="Sampul artikel">@else<em>Belum ada gambar</em>@endif</span>
        <div>
          <label class="btn btn-sm cl-pick"><input id="f-cover_file" type="file" name="cover_file" accept="image/png,image/jpeg,image/webp" hidden data-preview="#cover-prev">{{ $article->cover ? 'Ganti gambar' : 'Unggah gambar' }}</label>
          @if ($article->cover)<label class="check cl-rm" style="margin-top:8px"><input type="checkbox" name="cover_remove" value="1"> Hapus gambar sampul</label>@endif
          <small style="display:block;margin-top:8px">Tampil di daftar artikel dan di atas isi artikel. Rasio 16:9 paling rapi. JPG, PNG, atau WebP, maksimal 6 MB.</small>
        </div>
      </div>
      @error('cover_file')<em class="err">{{ $message }}</em>@enderror
    </div>
    <div class="field span2">
      <label for="f-body">Isi artikel<i>*</i></label>
      <div class="tools">
        <button type="button" data-fmt="h2" title="Judul bagian">H2</button>
        <button type="button" data-fmt="h3" title="Sub-judul">H3</button>
        <button type="button" data-fmt="p" title="Paragraf">¶</button>
        <span class="sep"></span>
        <button type="button" data-fmt="b" title="Tebal (Ctrl+B)"><b>B</b></button>
        <button type="button" data-fmt="i" title="Miring (Ctrl+I)"><i>I</i></button>
        <span class="sep"></span>
        <button type="button" data-fmt="ul" title="Daftar butir">• Daftar</button>
        <button type="button" data-fmt="ol" title="Daftar nomor">1. Daftar</button>
        <button type="button" data-fmt="a" title="Tautan">Tautan</button>
        <span class="sep"></span>
        <button type="button" id="insert-image" title="Sisipkan gambar ke isi artikel">Gambar</button>
        <input type="file" id="image-file" accept="image/png,image/jpeg,image/webp" hidden>
        <span class="sp"></span>
        <button type="button" id="toggle-preview">Pratinjau</button>
      </div>
      <textarea id="f-body" class="body-edit" name="body" rows="16" required>{{ old('body', $article->body) }}</textarea>
      <div class="preview" id="preview" hidden></div>
      <small>Gunakan tombol di atas untuk memformat. Tombol <strong>Gambar</strong> mengunggah foto dan menyisipkannya di posisi kursor.</small>
      @error('body')<em class="err">{{ $message }}</em>@enderror
    </div>

    <x-admin.field name="publish" label="Penerbitan" :value="$mode"
      :options="['draft' => 'Draf (belum tampil)', 'now' => 'Terbitkan', 'scheduled' => 'Jadwalkan']" id="f-publish" />
    <div class="field" id="when">
      <label for="f-published_at">Waktu terbit</label>
      <input id="f-published_at" type="datetime-local" name="published_at" value="{{ old('published_at', $article->published_at?->format('Y-m-d\TH:i')) }}">
      @error('published_at')<em class="err">{{ $message }}</em>@enderror
    </div>
  </div>

  <div class="actions">
    <button class="btn btn-primary" type="submit">Simpan</button>
    <a class="btn" href="{{ route('admin.articles.index') }}">Batal</a>
  </div>
</form>
@endsection
