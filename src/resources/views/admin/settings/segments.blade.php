@extends('admin.layout')

@section('title', 'Tab beranda')
@section('heading', 'Tab beranda')

@section('content')
<form method="post" action="{{ route('admin.segments.update') }}">
  @csrf @method('put')
  <p class="note" style="margin-bottom:16px">Beranda punya tiga tab pilihan pengunjung. Tentukan nama tab dan layanan apa saja yang tampil di dalamnya.</p>

  @foreach ($segments as $key => $segment)
    <section class="card">
      <h2>Tab “{{ $segment['label'] }}”</h2>
      <p class="sub">&nbsp;</p>
      <div class="field" style="max-width:340px;margin-bottom:16px">
        <label for="seg-{{ $key }}">Nama tab</label>
        <input id="seg-{{ $key }}" name="segments[{{ $key }}][label]" value="{{ old("segments.{$key}.label", $segment['label']) }}" maxlength="40" required>
      </div>
      <div class="sel-list">
        @foreach ($services as $slug => $service)
          <label>
            <input type="checkbox" name="segments[{{ $key }}][services][]" value="{{ $slug }}" @checked(in_array($slug, old("segments.{$key}.services", $segment['services']), true))>
            {{ $service['title'] }}
          </label>
        @endforeach
      </div>
    </section>
  @endforeach

  <div class="actions"><button class="btn btn-primary" type="submit">Simpan</button></div>
</form>
@include('admin.partials.reset', ['section' => 'segmen', 'custom' => $custom])
@endsection
