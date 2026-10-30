# Website PT Digital Space Komputer

Laravel 13 · PHP 8.5 (FPM) · Nginx · MariaDB 11.4, jalan di Docker. Cakupan mengikuti **Paket Bisnis**, plus menu Cek Harga (Konsep C).

| Halaman | URL | Konsep |
|---|---|---|
| Beranda (company profile) | `/` | B · Mitra IT Kantor |
| Layanan + 6 detail layanan | `/layanan`, `/layanan/{slug}` | B |
| Tentang & Klien, Artikel, Kontak (form minta penawaran) | `/tentang`, `/artikel`, `/kontak` | B |
| Landing iklan 1 (rumahan) | `/servis-panggilan` | A · Teknisi Datang |
| Landing iklan 2 (kantor) | `/it-support-kantor` | A |
| Cek Harga (estimasi biaya) | `/cek-harga` | C · Cek Harga & Status |
| Cek Status Servis (nomor tiket) | `/cek-status` | C |
| Sitemap | `/sitemap.xml` | |

Form booking dan form penawaran menyimpan data ke tabel `bookings`, lalu mengarahkan pengunjung ke WhatsApp dengan pesan yang sudah terisi.

## CMS admin

Semua konten (profil, layanan, harga, artikel, tiket servis, landing page, permintaan masuk) dikelola di
`/admin`. Buat akun pertama dengan `docker compose exec app php artisan admin:create`.
Panduan pemakaian: **[docs/CMS-ADMIN.md](docs/CMS-ADMIN.md)**.

## Instal di server klien

Ikuti **[docs/INSTALL-SERVER.md](docs/INSTALL-SERVER.md)** (server klien: Linux dengan CasaOS). Ringkasnya: `cp .env.example .env`, isi password, lalu `./prod.sh up -d --build`, dan sambungkan ke domain lewat Cloudflare Tunnel.

## Menjalankan (pengembangan)

```bash
docker compose up -d --build
```

Buka http://localhost:8095. Saat pertama kali jalan, container `app` memasang Composer, membuat `.env`, lalu menjalankan migrasi dan seeder. Contoh tiket servis: `DSK-1028`.

```bash
docker compose exec app php artisan test
```

## Yang perlu diganti sebelum tayang

- Data perusahaan, layanan, landing page, daftar harga, artikel, dan tiket diubah lewat CMS (`/admin`). Nilai awalnya ada di `src/config/site.php` dan seeder; yang diubah di CMS menimpa nilai awal itu.
- Email `info@digispacekom.net` di `config/site.php` adalah tebakan, bukan dari proposal. Ganti di CMS → Profil & kontak.
- Logo masih tiruan (`resources/views/partials/logo.blade.php`, `public/favicon.svg`) dan perlu diganti developer.
- Harga, artikel, tiket `DSK-1028`, dan janji layanan seperti garansi 30 hari masih contoh. Ganti atau hapus lewat CMS.
