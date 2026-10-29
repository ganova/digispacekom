# Panduan Instalasi di Server Klien

Website PT Digital Space Komputer · Laravel 13 · PHP 8.5 · Nginx · MariaDB 11.4 · Docker

Panduan ini untuk memasang website di server (atau PC) milik klien sampai bisa diakses
di **https://digispacekom.net** lewat Cloudflare Tunnel. Tidak perlu IP statis dan tidak
perlu membuka port router.

```
Pengunjung ──https──> Cloudflare (CDN, SSL) ──tunnel──> cloudflared ──> Nginx ──> PHP-FPM (Laravel) ──> MariaDB
                                                         └──────────── semua di Docker, di server klien ────────────┘
```

Perkiraan waktu: 45–60 menit.

---

## Daftar isi

1. [Persiapan](#1-persiapan)
2. [Pasang Docker](#2-pasang-docker)
3. [Ambil kode website](#3-ambil-kode-website)
4. [Isi konfigurasi `.env`](#4-isi-konfigurasi-env)
5. [Jalankan website](#5-jalankan-website)
6. [Hubungkan ke domain dengan Cloudflare Tunnel](#6-hubungkan-ke-domain-dengan-cloudflare-tunnel)
7. [Pengaturan Cloudflare](#7-pengaturan-cloudflare)
8. [Backup otomatis](#8-backup-otomatis)
9. [Cek akhir sebelum serah terima](#9-cek-akhir-sebelum-serah-terima)
10. [Perawatan: update, restart, log](#10-perawatan)
11. [Jika ada masalah](#11-jika-ada-masalah)
12. [Lampiran: memasang di Windows](#lampiran-memasang-di-windows)

---

## 1. Persiapan

**Spesifikasi minimum server**

| | Minimum | Disarankan |
|---|---|---|
| Sistem operasi | Ubuntu Server 22.04 / 24.04 (64-bit) | Ubuntu Server 24.04 LTS |
| RAM | 2 GB | 4 GB |
| Penyimpanan kosong | 10 GB | 20 GB SSD |
| Internet | Upload 10 Mbps | Upload 20 Mbps+ |

> Server Linux sangat disarankan. Windows bisa dipakai (lihat [lampiran](#lampiran-memasang-di-windows)),
> tetapi lebih lambat dan Docker Desktop baru jalan setelah pengguna login.

**Yang perlu disiapkan**

- [ ] Akses ke server: keyboard + monitor, atau SSH dari PC lain di jaringan yang sama.
- [ ] Akun pengguna di server yang bisa `sudo` (buat akun khusus, jangan minta password klien lewat chat).
- [ ] Akses ke akun **Cloudflare** tempat domain `digispacekom.net` dikelola.
      Kalau domain belum di Cloudflare, tambahkan dulu di dashboard Cloudflare lalu ganti nameserver
      di registrar domain (bisa butuh beberapa jam sampai aktif).
- [ ] Akses ke repo GitHub `ganova/digispacekom` (atau file zip kodenya).
- [ ] Pengaturan BIOS/OS: server tidak masuk mode sleep, dan otomatis menyala lagi setelah listrik padam
      (BIOS: *Restore on AC Power Loss* → *Power On*).

**Cek server** (jalankan di terminal server):

```bash
lsb_release -d     # versi Ubuntu
free -h            # RAM
df -h /            # sisa penyimpanan
```

Pastikan port **8080** belum dipakai aplikasi lain (WordPress lama, dsb.):

```bash
sudo ss -tlnp | grep -E ':8080 ' || echo "port 8080 bebas"
```

Kalau dipakai, pilih port lain nanti di langkah 4 (`WEB_PORT`).

---

## 2. Pasang Docker

```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install ca-certificates curl git
curl -fsSL https://get.docker.com | sudo sh
```

Izinkan akun Anda memakai Docker tanpa `sudo`, lalu **logout dan login lagi**:

```bash
sudo usermod -aG docker $USER
```

Setelah login lagi, pastikan Docker jalan dan otomatis menyala saat server boot:

```bash
docker --version
docker compose version        # harus versi 2.24 ke atas
sudo systemctl enable --now docker
docker run --rm hello-world   # harus muncul "Hello from Docker!"
```

---

## 3. Ambil kode website

Pilih **salah satu** cara.

### Cara A — git clone dengan deploy key (disarankan, mudah di-update)

Repo bersifat private. Buat kunci khusus server yang hanya bisa **membaca** repo:

```bash
ssh-keygen -t ed25519 -C "server-digispacekom" -f ~/.ssh/digispacekom -N ""
cat ~/.ssh/digispacekom.pub
```

Salin teks yang muncul, lalu di GitHub buka repo **ganova/digispacekom → Settings → Deploy keys →
Add deploy key**. Tempel kuncinya, beri judul `server-klien`, dan **jangan** centang *Allow write access*.

Lalu di server:

```bash
cat >> ~/.ssh/config <<'EOF'
Host github-digispacekom
  HostName github.com
  User git
  IdentityFile ~/.ssh/digispacekom
EOF

sudo mkdir -p /opt/digispacekom && sudo chown $USER: /opt/digispacekom
git clone git@github-digispacekom:ganova/digispacekom.git /opt/digispacekom
cd /opt/digispacekom
```

Ketik `yes` bila ditanya soal fingerprint GitHub.

### Cara B — salin file zip

Di PC developer (folder project):

```bash
git archive --format=zip -o digispacekom.zip HEAD
```

Salin `digispacekom.zip` ke server (flashdisk atau `scp`), lalu di server:

```bash
sudo apt -y install unzip
sudo mkdir -p /opt/digispacekom && sudo chown $USER: /opt/digispacekom
unzip digispacekom.zip -d /opt/digispacekom
cd /opt/digispacekom
chmod +x prod.sh
```

---

## 4. Isi konfigurasi `.env`

```bash
cd /opt/digispacekom
cp .env.example .env
```

Buat dua password acak untuk database:

```bash
openssl rand -hex 24
openssl rand -hex 24
```

Buka file `.env`:

```bash
nano .env
```

Isi seperti ini (ganti nilai yang ditandai):

```ini
APP_URL=https://digispacekom.net

DB_PASSWORD=<hasil openssl pertama>
DB_ROOT_PASSWORD=<hasil openssl kedua>

WEB_BIND=127.0.0.1
WEB_PORT=8080

SITE_WHATSAPP=6281385028980
SITE_PHONE=0813-8502-8980

CLOUDFLARE_TUNNEL_TOKEN=          # dikosongkan dulu, diisi di langkah 6
```

Simpan: `Ctrl+O`, `Enter`, lalu keluar dengan `Ctrl+X`. Amankan filenya:

```bash
chmod 600 .env
```

> **Penting:** password database ditentukan saat pertama kali jalan. Jika nanti ingin menggantinya,
> lihat bagian [11](#11-jika-ada-masalah).
> Simpan isi `.env` di password manager klien, jangan dikirim lewat chat.

---

## 5. Jalankan website

```bash
./prod.sh up -d --build
```

Saat pertama kali jalan, container akan:

1. memasang dependensi PHP (Composer), **3–10 menit** tergantung internet,
2. membuat kunci aplikasi,
3. membuat tabel database dan mengisi data awal (daftar harga, artikel contoh, tiket contoh),
4. menyimpan cache konfigurasi agar cepat.

Pantau prosesnya, lalu tunggu sampai muncul `ready to handle connections`:

```bash
./prod.sh logs -f app
```

Tekan `Ctrl+C` untuk berhenti memantau (website tetap jalan).

**Uji dari server itu sendiri:**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8080/      # harus 200
curl -s http://127.0.0.1:8080/up                                      # halaman cek kesehatan
./prod.sh ps                                                          # app, web, db = running
```

> Ingin melihat dari PC lain di jaringan kantor sebelum online? Ubah `WEB_BIND=0.0.0.0` di `.env`,
> jalankan `./prod.sh up -d`, lalu buka `http://<IP-server>:8080`. Kembalikan ke `127.0.0.1` setelah selesai.

---

## 6. Hubungkan ke domain dengan Cloudflare Tunnel

> Jika `digispacekom.net` saat ini mengarah ke WordPress lama, langkah ini **menggantikannya** dengan website baru.
> Backup WordPress lama dulu bila masih diperlukan.

1. Login ke **dash.cloudflare.com** → menu kiri **Zero Trust** (pertama kali: pilih paket **Free**).
2. Buka **Networks → Tunnels → Create a tunnel**.
3. Pilih **Cloudflared**, beri nama `digispacekom-server`, lalu **Save tunnel**.
4. Di halaman *Install and run connector*, pilih **Docker**. Akan muncul perintah seperti:
   `docker run cloudflare/cloudflared:latest tunnel --no-autoupdate run --token eyJhIjoi...`
   **Salin hanya teks token** (mulai dari `eyJ...` sampai akhir). Jangan jalankan perintah itu.
5. Tempel token ke `.env` di server:

   ```bash
   nano .env
   # CLOUDFLARE_TUNNEL_TOKEN=eyJhIjoi....
   ```

6. Jalankan ulang, dan container `cloudflared` ikut menyala otomatis:

   ```bash
   ./prod.sh up -d
   ./prod.sh logs --tail 20 cloudflared     # cari "Registered tunnel connection"
   ```

   Di dashboard, status tunnel berubah menjadi **HEALTHY**. Klik **Next**.
7. Tab **Public Hostname → Add a public hostname**:

   | Kolom | Isi |
   |---|---|
   | Subdomain | *(kosong)* |
   | Domain | `digispacekom.net` |
   | Type | `HTTP` |
   | URL | `web:80` |

   Simpan. Ulangi sekali lagi untuk **Subdomain** `www` dengan URL yang sama.

   > Jika muncul error *"A record with that host already exists"*, buka **DNS → Records** di dashboard domain,
   > hapus record `A`/`CNAME` lama untuk `digispacekom.net` / `www`, lalu ulangi.

8. Buka **https://digispacekom.net** dari HP (pakai data seluler, bukan WiFi kantor).

---

## 7. Pengaturan Cloudflare

Di dashboard domain `digispacekom.net`:

| Menu | Pengaturan |
|---|---|
| **SSL/TLS → Overview** | Mode **Full** |
| **SSL/TLS → Edge Certificates** | **Always Use HTTPS**: On · **Automatic HTTPS Rewrites**: On |
| **Speed → Optimization** | **Brotli**: On (jika tersedia) |
| **Caching → Configuration** | **Browser Cache TTL**: *Respect Existing Headers* |
| **Rules → Redirect Rules** | `www.digispacekom.net/*` → `https://digispacekom.net/${1}` (301), agar hanya satu alamat |

File CSS, JS, dan gambar sudah dikirim dengan header cache 30 hari, sehingga otomatis disimpan Cloudflare.

**Redirect domain lama** (bila `digispacekomp.net` masih dikelola klien dan sudah di Cloudflare):
buat Redirect Rule di domain lama: *All incoming requests* → *Dynamic* →
`concat("https://digispacekom.net", http.request.uri.path)` dengan status **301**.

---

## 8. Backup otomatis

Backup manual:

```bash
./prod.sh backup         # tersimpan di /opt/digispacekom/backups/, otomatis menyimpan 14 terakhir
```

Jadwalkan setiap hari pukul 02.00:

```bash
crontab -e
```

Tambahkan baris ini di paling bawah:

```cron
0 2 * * * /opt/digispacekom/prod.sh backup >> /opt/digispacekom/backups/cron.log 2>&1
```

> Backup yang hanya ada di server rumah ikut hilang bila disknya rusak. Salin folder `backups/`
> secara berkala ke Google Drive/flashdisk. Paket Pro sudah mencakup backup otomatis ke luar server.

**Mengembalikan backup:**

```bash
gunzip -c backups/db-YYYYMMDD-HHMM.sql.gz | ./prod.sh exec -T db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" digispacekom'
```

---

## 9. Cek akhir sebelum serah terima

- [ ] `https://digispacekom.net` terbuka dengan gembok (https), di HP dan laptop.
- [ ] Menu Layanan, Tentang & Klien, Artikel, Kontak, Cek Harga, dan Cek Status terbuka semua.
- [ ] `/servis-panggilan` dan `/it-support-kantor` (landing iklan) terbuka.
- [ ] Isi form booking → WhatsApp terbuka ke nomor yang benar dengan pesan terisi.
- [ ] Isi form *Minta penawaran* di halaman Kontak → WhatsApp terbuka.
- [ ] Tombol WhatsApp melayang mengarah ke nomor yang benar.
- [ ] Halaman yang tidak ada (mis. `/abc`) menampilkan "404 Not Found" tanpa detail error.
- [ ] Uji kecepatan di **pagespeed.web.dev** (versi HP), lalu simpan hasilnya untuk laporan sebelum/sesudah.
- [ ] Daftarkan `https://digispacekom.net/sitemap.xml` di **Google Search Console**.
- [ ] Matikan server sebentar lalu nyalakan lagi: website harus kembali online sendiri dalam 1–2 menit.
- [ ] Cron backup terpasang (`crontab -l`) dan `./prod.sh backup` berhasil.

**Konten yang masih contoh dan perlu diganti sebelum tayang:**

| Konten | Lokasi |
|---|---|
| Logo (masih tiruan) | `src/resources/views/partials/logo.blade.php`, `src/public/favicon.svg` |
| Data perusahaan, layanan, klien, area, jam buka | `src/config/site.php` |
| Daftar estimasi harga | `src/database/seeders/PriceItemSeeder.php` |
| Artikel | `src/database/seeders/ArticleSeeder.php` |
| Tiket contoh `DSK-1028` | `src/database/seeders/ServiceTicketSeeder.php` |
| Janji layanan (garansi 30 hari, dsb.) | `src/config/site.php` dan teks di `src/resources/views/` |

Seeder hanya dijalankan otomatis saat database **masih kosong** (instalasi pertama).

---

## 10. Perawatan

Semua perintah dijalankan dari `/opt/digispacekom`.

| Keperluan | Perintah |
|---|---|
| Status container | `./prod.sh ps` |
| Log aplikasi | `./prod.sh logs -f app` |
| Log Nginx | `./prod.sh logs -f web` |
| Log tunnel | `./prod.sh logs -f cloudflared` |
| Restart semua | `./prod.sh restart` |
| Matikan | `./prod.sh down` *(data tetap aman)* |
| Nyalakan | `./prod.sh up -d` |
| Lihat data booking yang masuk | `./prod.sh exec db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" digispacekom -e "select id,type,name,device,location,created_at from bookings order by id desc limit 20"'` |

**Update website** setelah ada perubahan kode:

```bash
./prod.sh backup                 # selalu backup dulu
git pull                         # (Cara B: ekstrak zip baru di atas folder lama, .env jangan ditimpa)
./prod.sh up -d --build
./prod.sh logs -f app            # tunggu "ready to handle connections"
```

Migrasi database dan cache dijalankan otomatis saat container menyala.

> **Jangan** menjalankan `./prod.sh down -v`, karena opsi `-v` **menghapus database**.

---

## 11. Jika ada masalah

| Gejala | Penyebab & solusi |
|---|---|
| `502 Bad Gateway` setelah baru dinyalakan | App masih memasang Composer / menunggu database. Tunggu, cek `./prod.sh logs -f app`. |
| Log app terus `waiting for database...` | Cek `./prod.sh logs db`. Bila password di `.env` diubah setelah instalasi pertama, kembalikan ke password lama (lihat di bawah). |
| `port is already allocated` | Port 8080 dipakai aplikasi lain. Ganti `WEB_PORT` di `.env`, lalu `./prod.sh up -d`. |
| Tunnel tidak **HEALTHY** | `./prod.sh logs cloudflared`. Pastikan token disalin lengkap tanpa spasi dan server punya internet. |
| Domain menampilkan error 1033 | Container `cloudflared` mati. Jalankan `./prod.sh up -d`. |
| Domain menampilkan error 502 dari Cloudflare | Public hostname salah. URL harus `web:80` dengan type `HTTP`. |
| Tampilan berantakan / CSS tidak muncul | Bersihkan cache Cloudflare: **Caching → Purge Everything**. |
| Form booking error **419** | Halaman dibuka terlalu lama (sesi habis). Muat ulang halaman lalu kirim lagi. |
| Website tidak kembali setelah listrik padam | `sudo systemctl is-enabled docker` harus `enabled`. Cek pengaturan BIOS *Power On after AC loss*. |

**Mengganti password database setelah instalasi:**

```bash
./prod.sh exec db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" -e "ALTER USER '\''digispacekom'\''@'\''%'\'' IDENTIFIED BY '\''PASSWORD_BARU'\''"'
nano .env                 # ubah DB_PASSWORD=PASSWORD_BARU
./prod.sh up -d
```

Untuk masalah lain, kirim hasil `./prod.sh ps` dan `./prod.sh logs --tail 100 app` ke developer
(periksa dulu agar tidak ada password di dalamnya).

---

## Lampiran: memasang di Windows

Gunakan hanya bila server klien memakai Windows 10/11 Pro dan tidak bisa diganti Linux.

1. Aktifkan WSL2: buka **PowerShell sebagai Administrator**, jalankan `wsl --install -d Ubuntu-24.04`, restart,
   lalu buat username/password Ubuntu saat diminta.
2. Pasang **Docker Desktop** dari docker.com. Di **Settings**:
   - *General* → centang **Use the WSL 2 based engine** dan **Start Docker Desktop when you sign in**.
   - *Resources → WSL integration* → aktifkan untuk **Ubuntu-24.04**.
3. Buka aplikasi **Ubuntu** dari Start Menu, lalu ikuti langkah **3 sampai 8** di atas dari dalam Ubuntu.
   Simpan project di `/opt/digispacekom` (di dalam Ubuntu), **bukan** di `C:\`, karena di `C:\` situs jauh lebih lambat.
4. Agar website kembali online setelah restart:
   - atur Windows login otomatis (*netplwiz*) atau biarkan akun selalu login,
   - **Settings → System → Power** → *Sleep: Never*,
   - container sudah memakai `restart: unless-stopped`, jadi ikut menyala bersama Docker Desktop.
5. Untuk cron backup di Windows, gunakan **Task Scheduler** yang menjalankan:
   `wsl -d Ubuntu-24.04 -- /opt/digispacekom/prod.sh backup` setiap hari pukul 02.00.
