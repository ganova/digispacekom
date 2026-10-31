<section class="card">
  <div class="card-head">
    <div><h2>Mulai dari sini</h2><p class="sub">Pekerjaan yang bisa Anda lakukan.</p></div>
  </div>
  <ul class="feed">
    <li><a href="{{ route('admin.tickets.create') }}"><span class="dot">@include('admin.partials.icon', ['name' => 'ticket', 'size' => 17])</span><span class="body"><strong>Buat tiket servis</strong><span>Catat perangkat yang baru diterima dan perbarui tahapannya.</span></span>@include('admin.partials.icon', ['name' => 'arrow', 'size' => 15])</a></li>
    <li><a href="{{ route('admin.prices.index') }}"><span class="dot">@include('admin.partials.icon', ['name' => 'tag', 'size' => 17])</span><span class="body"><strong>Daftar harga &amp; suku cadang</strong><span>Tambah atau ubah item dan harga yang tampil di Cek Harga.</span></span>@include('admin.partials.icon', ['name' => 'arrow', 'size' => 15])</a></li>
    <li><a href="{{ route('admin.articles.create') }}"><span class="dot">@include('admin.partials.icon', ['name' => 'doc', 'size' => 17])</span><span class="body"><strong>Tulis artikel</strong><span>Simpan sebagai draf atau terbitkan langsung.</span></span>@include('admin.partials.icon', ['name' => 'arrow', 'size' => 15])</a></li>
  </ul>
</section>
