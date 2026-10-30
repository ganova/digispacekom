# Panduan CMS Admin

CMS ada di `https://digispacekom.net/admin` (lokal: `http://localhost:8095/admin`). Semua isi website bisa diubah
dari sini tanpa menyentuh kode. Perubahan langsung tampil di situs.

## Masuk

Buka `/admin`, isi email dan password. Akun pertama dibuat di server:

```bash
./prod.sh exec app php artisan admin:create
```

Akun tambahan dibuat di menu **Daftar admin**. Semua admin punya akses penuh. Ganti password sendiri di
**Akun saya**.

## Menu

| Menu | Untuk apa |
|---|---|
| **Dasbor** | Ringkasan: permintaan baru, jumlah tiket, harga, artikel. |
| **Permintaan masuk** | Semua isian form booking dan minta penawaran. Ubah status (Baru, Sudah dihubungi, Selesai, Batal), tulis catatan internal, cari, dan unduh CSV untuk Excel. Pengunjung juga sudah diarahkan ke WhatsApp, jadi data kontaknya ada di chat. |
| **Tiket servis** | Buat tiket untuk perangkat yang diterima, lalu perbarui tahapannya. Pelanggan melihatnya di `/cek-status` dengan nomor tiket (nama tampil tersamar). |
| **Daftar harga** | Item estimasi di halaman `/cek-harga`. Ada pratinjau tampilan harga saat mengetik. |
| **Artikel** | Tulis, jadwalkan, atau simpan sebagai draf. Editor punya tombol format dan pratinjau. |
| **Layanan** | Enam halaman layanan (tambah, ubah, hapus, urutkan). |
| **Tab beranda** | Nama tab Kantor / Rumah / Sekolah dan layanan yang tampil di tiap tab. |
| **Landing page iklan** | Judul, poin, formulir, dan teks mesin pencari untuk dua halaman iklan. |
| **Profil & kontak** | Nama perusahaan, telepon, WhatsApp, email, alamat, jam buka. |
| **Klien, wilayah, langkah** | Daftar klien, wilayah layanan, dan tiga langkah layanan. |

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

## Yang belum bisa lewat CMS

- Logo dan warna (perlu developer).
- Menambah landing page iklan baru atau mengubah alamatnya (isi tiap landing bisa diubah).
- Mengunggah gambar. Situs ini belum memakai foto.

## Catatan teknis

- Konten situs (profil, layanan, dan sebagainya) disimpan di tabel `settings` dan menimpa nilai bawaan di
  `src/config/site.php`. Tabel lain: `price_items`, `articles`, `service_tickets`, `bookings`.
- Isi artikel adalah HTML yang ditulis admin dan tidak disaring. Karena itu hanya berikan akun admin kepada
  orang yang dipercaya.
- Halaman admin diberi `noindex` dan `Cache-Control: no-store`. Percobaan login dibatasi 6 per menit.
- Backup database (`./prod.sh backup`) sudah mencakup semua konten CMS.
