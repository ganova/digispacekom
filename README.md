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

## Instal di server klien

Ikuti **[docs/INSTALL-SERVER.md](docs/INSTALL-SERVER.md)** (jalur utama: Windows 11 + WSL2 + Docker Desktop; ada catatan untuk Linux). Ringkasnya: `cp .env.example .env`, isi password, lalu `./prod.sh up -d --build`, dan sambungkan ke domain lewat Cloudflare Tunnel.

## Menjalankan (pengembangan)

```bash
docker compose up -d --build
```

Buka http://localhost:8095. Saat pertama kali jalan, container `app` memasang Composer, membuat `.env`, lalu menjalankan migrasi dan seeder. Contoh tiket servis: `DSK-1028`.

```bash
docker compose exec app php artisan test
```

## Yang perlu diganti sebelum tayang

- Data perusahaan, layanan, dan landing page ada di `src/config/site.php`. Nomor WA diatur lewat `SITE_WHATSAPP` / `SITE_PHONE` di `.env`.
- Logo masih tiruan (`resources/views/partials/logo.blade.php`, `public/favicon.svg`).
- Daftar harga (`PriceItemSeeder`), artikel (`ArticleSeeder`), dan janji layanan seperti garansi 30 hari masih contoh.
- Tiket servis dan daftar harga belum punya halaman admin. Admin yang memperbarui status dari HP termasuk Paket Pro.
