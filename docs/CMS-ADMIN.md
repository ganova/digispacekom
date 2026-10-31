# Panduan CMS Admin

CMS ada di `https://digispacekom.net/admin` (lokal: `http://localhost:8095/admin`). Semua isi website bisa diubah
dari sini tanpa menyentuh kode. Perubahan langsung tampil di situs.

## Masuk

Buka `/admin`, isi email dan password. Akun pertama dibuat di server:

```bash
./prod.sh exec app php artisan admin:create
```

Akun tambahan dibuat di menu **Pengguna & peran**. Ganti password sendiri di **Akun saya**.

### Peran

| Peran | Bisa melakukan |
|---|---|
| **Admin penuh** | Semua menu di bawah, termasuk permintaan masuk, isi situs, dan mengelola pengguna. |
| **Staf** | Hanya **Tiket servis**, **Daftar harga** (termasuk suku cadang dan kategorinya), dan **Artikel**, ditambah **Akun saya**. Menu lain tidak tampil, dan bila alamatnya dibuka langsung, akses ditolak. |

Akun yang dibuat lewat perintah `admin:create` selalu admin penuh. Staf dibuat di **Pengguna & peran → Tambah
pengguna**, dengan memilih peran *Staf*. Peran dan password pengguna bisa diubah lewat tombol **Ubah**. Minimal harus
ada satu admin penuh, dan akun yang sedang dipakai tidak bisa dihapus atau diturunkan perannya.

## Menu

| Menu | Untuk apa |
|---|---|
| **Dasbor** | Ringkasan: permintaan baru, jumlah tiket, harga, artikel. Staf melihat versi ringkas tanpa data permintaan. |
| **Permintaan masuk** | Semua isian form booking dan minta penawaran. Ubah status (Baru, Sudah dihubungi, Selesai, Batal), tulis catatan internal, cari, dan unduh CSV untuk Excel. Pengunjung juga sudah diarahkan ke WhatsApp, jadi data kontaknya ada di chat. |
| **Tiket servis** | Buat tiket untuk perangkat yang diterima, lalu perbarui tahapannya. Pelanggan melihatnya di `/cek-status` dengan nomor tiket (nama tampil tersamar). |
| **Daftar harga** | Item estimasi di halaman `/cek-harga`, dengan **foto** dan lencana **Ready stok / Stok habis** (untuk suku cadang). Ada pratinjau tampilan harga saat mengetik. Tombol **Kelola kategori** menambah kategori baru seperti *Sparepart Ready*, *CCTV*, atau *Game Station*. |
| **Artikel** | Tulis, jadwalkan, atau simpan sebagai draf. Bisa memakai **gambar sampul** dan **menyisipkan gambar** ke isi artikel lewat tombol *Gambar* di editor. Editor punya tombol format dan pratinjau. |
| **Layanan** | Halaman layanan (tambah, ubah, hapus, urutkan). Pilih ikon (termasuk kamera/CCTV, game, WiFi). Daftar masalah boleh dikosongkan. |
| **Tab beranda** | Tab di beranda bisa **ditambah, dihapus, diberi nama, dan diurutkan** (maks. 8), mis. *CCTV* atau *Game Station*. Tiap tab memilih layanannya sendiri. |
| **Landing page iklan** | Judul, poin, formulir, dan teks mesin pencari untuk dua halaman iklan. |
| **Profil & kontak** | Nama perusahaan, telepon, WhatsApp, email, alamat, jam buka. |
| **Klien & logo** | Nama klien yang dipercaya, dengan **logo opsional** (PNG/JPG/WebP, maks. 2 MB). Bisa diurutkan dengan menyeret nomornya. Tampil di beranda, landing page iklan, dan halaman Tentang. |
| **Wilayah & langkah** | Wilayah layanan dan tiga langkah layanan. |
| **Pengguna & peran** | Tambah, ubah, dan hapus akun admin atau staf. |

## Pintasan & fitur tampilan

| Pintasan | Fungsi |
|---|---|
| `Ctrl+K` atau `/` | Palet pencarian: lompat ke halaman, buat baru, atau cari tiket/permintaan/harga/artikel |
| `Ctrl+S` | Simpan formulir yang sedang diubah |
| `Ctrl+B` / `Ctrl+I` | Tebal / miring di editor artikel |
| Ikon bulan/matahari | Tema gelap atau terang (diingat per perangkat) |
| Ikon panel di kiri atas | Ringkas menu samping menjadi ikon saja |

- Saat formulir diubah, muncul bilah **Ada perubahan belum disimpan** di bawah layar. Browser juga
  memperingatkan bila halaman ditinggalkan sebelum disimpan.
- Klik baris mana pun di tabel untuk membukanya.
- Tahapan tiket bisa diurutkan dengan menyeret nomornya.
- Di HP, menu cepat (Dasbor, Masuk, Tiket, Harga, Lainnya) ada di bawah layar.
- Dasbor punya grafik permintaan 14 hari dan daftar periksa **Siap tayang** untuk konten contoh yang belum diganti.

## Tampilan gelap di situs publik

Situs untuk pelanggan punya tombol tema di header (ikon setengah lingkaran, matahari, atau bulan):

| Mode | Perilaku |
|---|---|
| **Otomatis** (bawaan) | Gelap pukul **18.00–05.59** menurut jam di perangkat pengunjung, terang di luar jam itu. Berganti sendiri tanpa memuat ulang halaman. |
| **Terang** / **Gelap** | Pilihan manual pengunjung, diingat di perangkat itu. |

Jam otomatis ditentukan di skrip kecil pada `src/resources/views/layouts/base.blade.php` dan `src/public/js/site.js`
(`h >= 18 || h < 6`) bila klien ingin jam lain.

## Alur kerja sehari-hari

**Servis masuk:** buat tiket (nomor `DSK-xxxx` terisi otomatis), beri nomornya ke pelanggan. Saat status
berubah, buka tiket, ubah tahap yang sedang berjalan jadi **Selesai**, lalu tahap berikutnya jadi
**Sedang berjalan**, dan simpan.

**Harga berubah:** Daftar harga → cari item → Ubah.

**Nomor WhatsApp berganti:** Profil & kontak → ubah nomor (boleh diketik `0813-…`). Tombol WhatsApp di seluruh
situs ikut berubah.

## Mengembalikan ke isi awal

Halaman Profil, Layanan, Tab beranda, Landing, dan Klien/wilayah/langkah punya tautan
**Kembalikan ke isi awal** (muncul bila ada perubahan). Isi awal berasal dari `src/config/site.php`.

**Menambah tab beranda baru (mis. CCTV, Game Station):** buat dulu layanannya di *Layanan → Tambah layanan*, lalu buka *Tab beranda → Tambah tab*, beri nama, centang layanannya, dan simpan.

**Menambah kategori sparepart ready:** *Daftar harga → Kelola kategori → Tambah* (mis. “Sparepart Ready”). Lalu tambahkan item dengan kategori itu, isi **Status stok = Ready stok**, dan unggah fotonya.

**Menambah klien lain:** Klien & logo → *Tambah klien* → isi nama (dan unggah logo bila ada) → *Simpan*. Logo tampil
sebagai kotak putih agar terbaca di latar gelap maupun terang; logo berlatar transparan paling rapi.

## Yang belum bisa lewat CMS

- Logo dan warna (perlu developer).
- Menambah landing page iklan baru atau mengubah alamatnya (isi tiap landing bisa diubah).
- Mengunggah gambar selain logo klien, sampul dan isi artikel, serta foto item harga.

## Catatan teknis

- Konten situs (profil, layanan, dan sebagainya) disimpan di tabel `settings` dan menimpa nilai bawaan di
  `src/config/site.php`. Tabel lain: `price_items`, `articles`, `service_tickets`, `bookings`.
- Isi artikel adalah HTML yang ditulis admin dan tidak disaring. Karena itu hanya berikan akun admin kepada
  orang yang dipercaya.
- Halaman admin diberi `noindex` dan `Cache-Control: no-store`. Percobaan login dibatasi 6 per menit.
- Backup database (`./prod.sh backup`) mencakup semua konten CMS, dan juga menyimpan logo klien dari
  `src/public/uploads` (logo, sampul, foto) ke `backups/uploads-*.tgz`.
- Gambar disimpan di `src/public/uploads/` (`clients`, `articles`, `prices`) dengan nama acak. Hanya PNG/JPG/WebP yang
  diterima (SVG ditolak karena bisa memuat skrip). Foto besar otomatis dikecilkan (maks. 1400 px untuk artikel, 900 px
  untuk item harga) dan diubah ke WebP agar halaman cepat dibuka di HP. Ukuran unggahan maksimal 6 MB (logo 2 MB).
