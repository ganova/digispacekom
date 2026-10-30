@if ($custom ?? false)
<form method="post" action="{{ route('admin.reset', $section) }}" data-confirm="Kembalikan bagian ini ke isi awal? Perubahan Anda akan hilang." style="margin-top:14px">
  @csrf
  <button class="link-btn muted" type="submit">Kembalikan ke isi awal</button>
</form>
@endif
