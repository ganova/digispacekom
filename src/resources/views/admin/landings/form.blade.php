@extends('admin.layout')

@section('title', 'Ubah landing page')
@section('heading', 'Landing /'.$slug)

@section('actions')
  <a class="btn" href="{{ route('admin.landings.index') }}">← Kembali</a>
  <a class="btn" href="{{ route('landing', $slug) }}" target="_blank" rel="noopener">Lihat halaman</a>
@endsection

@section('content')
<form method="post" action="{{ route('admin.landings.update', $slug) }}">
  @csrf @method('put')

  <section class="card">
    <h2>Teks utama</h2>
    <p class="sub">Judul besar di bagian paling atas halaman.</p>
    <div class="grid g2">
      <x-admin.field name="headline" label="Judul utama" :value="$page['headline']" required />
      <x-admin.field name="highlight" label="Kalimat sorotan" :value="$page['highlight']" required hint="Tampil berwarna di bawah judul." />
      <x-admin.field name="lead" label="Kalimat pembuka" :rows="3" :value="$page['lead']" required class="span2 field" />
      <x-admin.field name="checks" label="Poin keunggulan" :rows="4" :value="implode("\n", $page['checks'])" required class="span2 field" hint="Satu baris satu poin (disarankan 3)." />
    </div>
  </section>

  <section class="card">
    <h2>Formulir</h2>
    <p class="sub">Isian pengunjung dikirim ke menu Permintaan masuk dan diteruskan ke WhatsApp.</p>
    <div class="grid g2">
      <x-admin.field name="form_title" label="Judul formulir" :value="$page['form_title']" required />
      <x-admin.field name="placeholder" label="Contoh isian keluhan" :value="$page['placeholder']" required />
      <x-admin.field name="devices" label="Pilihan perangkat" :rows="5" :value="implode("\n", $page['devices'])" required class="span2 field" hint="Satu baris satu pilihan." />
    </div>
  </section>

  <section class="card">
    <h2>Mesin pencari</h2>
    <p class="sub">Teks yang tampil di tab browser dan hasil Google.</p>
    <div class="grid">
      <x-admin.field name="meta_title" label="Judul tab" :value="$page['meta_title']" required />
      <x-admin.field name="meta_description" label="Deskripsi" :rows="3" :value="$page['meta_description']" required hint="Sekitar 150 karakter." />
    </div>
    <div class="actions">
      <button class="btn btn-primary" type="submit">Simpan</button>
      <a class="btn" href="{{ route('admin.landings.index') }}">Batal</a>
    </div>
  </section>
</form>
@endsection
