# Panduan Instalasi di Server Klien (CasaOS)

Website PT Digital Space Komputer · Laravel 13 · PHP 8.5 · Nginx · MariaDB 11.4 · Docker

Panduan ini memasang website di **server CasaOS** milik klien (PC terpisah yang menjalankan Linux + CasaOS)
sampai bisa diakses di **https://digispacekom.net** lewat Cloudflare Tunnel. Tidak perlu IP statis dan tidak
perlu membuka port router.

```
Pengunjung ──https──> Cloudflare (CDN, SSL) ──tunnel──> cloudflared ──> Nginx ──> PHP-FPM (Laravel) ──> MariaDB
                                                         └──── semua di Docker, di server CasaOS ────┘
```

CasaOS sudah membawa **Docker**, jadi tidak ada yang perlu dipasang selain `git`. Website dipasang lewat
**terminal**, bukan lewat app store CasaOS, supaya update dan backup bisa memakai skrip `prod.sh`.
Windows 11 di kantor klien hanya dipakai untuk membuka terminal/SSH dan browser.

Perkiraan waktu: 30–45 menit.

---

## Daftar isi

1. [Persiapan dan cek server](#1-persiapan-dan-cek-server)
2. [Ambil kode website](#2-ambil-kode-website)
3. [Isi konfigurasi `.env`](#3-isi-konfigurasi-env)
4. [Jalankan website](#4-jalankan-website)
5. [Hubungkan ke domain dengan Cloudflare Tunnel](#5-hubungkan-ke-domain-dengan-cloudflare-tunnel)
6. [Pengaturan Cloudflare](#6-pengaturan-cloudflare)
7. [Backup otomatis](#7-backup-otomatis)
8. [Jalan otomatis setelah restart](#8-jalan-otomatis-setelah-restart)
9. [Cek akhir sebelum serah terima](#9-cek-akhir-sebelum-serah-terima)
10. [Perawatan: update, restart, log](#10-perawatan)
11. [Jika ada masalah](#11-jika-ada-masalah)
12. [Catatan tentang dashboard CasaOS](#12-catatan-tentang-dashboard-casaos)

---

## 1. Persiapan dan cek server

**Yang perlu disiapkan**

- [ ] Akses terminal ke server CasaOS: layar dan keyboard di server itu, atau SSH dari Windows 11.
      Untuk SSH, buka **Terminal / PowerShell** di Windows lalu ketik `ssh <username>@<IP-server>`
      (IP server terlihat di dashboard CasaOS atau di router). Gunakan akun khusus, jangan minta password lewat chat.
- [ ] Akses ke akun **Cloudflare** tempat domain `digispacekom.net` dikelola.
      Jika domain belum di Cloudflare, tambahkan dulu di dashboard Cloudflare lalu ganti nameserver di
      registrar domain (bisa butuh beberapa jam sampai aktif).
- [ ] Repo kode bersifat publik selama masa development: `https://github.com/ganova/digispacekom`
- [ ] Di BIOS server: **Restore on AC Power Loss → Power On**, agar server menyala sendiri setelah listrik padam.

**Cek server** (jalankan di terminal server dan simpan hasilnya):

```bash
whoami
cat /etc/os-release | head -2          # Debian/Ubuntu/dll.
uname -m                               # x86_64 = PC biasa, aarch64 = ARM
free -h                                # RAM minimal 2 GB, disarankan 4 GB
df -h /                                # sisa penyimpanan minimal 10 GB
docker --version
docker compose version                 # harus v2.24 atau lebih baru (lihat catatan di bawah)
docker ps                              # daftar container yang sudah jalan
sudo ss -tlnp | grep -E ':8080 ' || echo "port 8080 bebas"
```

**Jika `docker ps` menjawab "permission denied"**, tambahkan akun ke grup Docker lalu logout dan login lagi:

```bash
sudo usermod -aG docker $USER
```

**Jika `docker compose version` tidak ada atau lebih lama dari v2.24**, pasang versi terbaru:

```bash
sudo mkdir -p /usr/local/lib/docker/cli-plugins
ARCH=$(uname -m)        # x86_64 atau aarch64
sudo curl -SL "https://github.com/docker/compose/releases/latest/download/docker-compose-linux-$ARCH" \
  -o /usr/local/lib/docker/cli-plugins/docker-compose
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose
docker compose version
```

**Jika port 8080 dipakai** aplikasi CasaOS lain, pilih port lain (mis. 8095) dan isi di `WEB_PORT` pada langkah 3.

**Jika `git` belum ada:**

```bash
sudo apt update && sudo apt -y install git curl ca-certificates
```

---

## 2. Ambil kode website

```bash
sudo mkdir -p /opt/digispacekom && sudo chown $USER: /opt/digispacekom
git clone https://github.com/ganova/digispacekom.git /opt/digispacekom
cd /opt/digispacekom
git log --oneline | head -3
```

> Tidak perlu login GitHub karena repo publik. Jika nanti repo dijadikan private, pakai *deploy key*
> (GitHub → repo → Settings → Deploy keys, hanya izin baca) atau `gh auth login`.

---

## 3. Isi konfigurasi `.env`

```bash
cd /opt/digispacekom
cp .env.example .env
```

Buat dua password acak:

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

CLOUDFLARE_TUNNEL_TOKEN=          # dikosongkan dulu, diisi di langkah 5
```

Simpan: `Ctrl+O`, `Enter`, keluar dengan `Ctrl+X`. Amankan filenya:

```bash
chmod 600 .env
```

> `.env` **tidak pernah** boleh di-commit ke GitHub (sudah diblok oleh `.gitignore`), karena repo ini publik.
> Simpan isinya di password manager klien. Jika `DB_PASSWORD` kosong, website sengaja menolak menyala.
> Password database ditentukan saat pertama kali jalan; mengubahnya sesudahnya butuh langkah khusus (lihat [11](#11-jika-ada-masalah)).

---

## 4. Jalankan website

```bash
./prod.sh up -d --build
```

> Bila muncul `permission denied` pada `./prod.sh`, jalankan `chmod +x prod.sh` sekali.
> Bila akun belum di grup Docker, awali dengan `sudo` (mis. `sudo ./prod.sh up -d --build`).

Saat pertama kali jalan, container akan:

1. memasang dependensi PHP (Composer), **3–10 menit** tergantung internet,
2. membuat kunci aplikasi,
3. membuat tabel database dan mengisi data awal (daftar harga, artikel contoh, tiket contoh),
4. menyimpan cache konfigurasi agar cepat.

Pantau sampai muncul `ready to handle connections`:

```bash
./prod.sh logs -f app
```

`Ctrl+C` untuk berhenti memantau (website tetap jalan).

**Uji dari server itu sendiri:**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8080/    # harus 200
./prod.sh ps                                                        # app, web, db = running
```

**Lihat tampilannya dari Windows 11** sebelum online: ubah `WEB_BIND=0.0.0.0` di `.env`, jalankan
`./prod.sh up -d`, lalu buka `http://<IP-server>:8080` di browser Windows. Kembalikan ke `127.0.0.1`
dan jalankan `./prod.sh up -d` lagi setelah selesai (agar website tidak terbuka di jaringan kantor).

**Buat akun admin CMS** (sekali, setelah website jalan). Password diketik di terminal dan tidak tampil di layar:

```bash
./prod.sh exec app php artisan admin:create
```

Masukkan email, nama, lalu password (minimal 10 karakter). Akun ini berperan **admin penuh**; staf dengan akses
terbatas (artikel, tiket servis, daftar harga) dibuat di CMS pada menu **Pengguna & peran**. Setelah website online, CMS dibuka di
`https://digispacekom.net/admin`. Panduan pemakaiannya ada di [CMS-ADMIN.md](CMS-ADMIN.md).

---

## 5. Hubungkan ke domain dengan Cloudflare Tunnel

> Jika `digispacekom.net` saat ini mengarah ke WordPress lama, langkah ini **menggantikannya** dengan website baru.
> Backup WordPress lama dulu bila masih diperlukan.

**Cek dulu apakah server sudah punya tunnel Cloudflare** (mis. dari app CasaOS atau WordPress lama):

```bash
docker ps --format '{{.Names}}  {{.Image}}' | grep -i cloudflared || echo "belum ada tunnel"
```

Jika sudah ada, tidak masalah. Kita membuat tunnel **baru** khusus website ini, dan nanti hanya perlu
menghapus hostname `digispacekom.net` dari tunnel lama (langkah 6 di bawah) bila bentrok.

1. Login ke **dash.cloudflare.com** → menu kiri **Zero Trust** (pertama kali: pilih paket **Free**).
2. **Networks → Tunnels → Create a tunnel** → pilih **Cloudflared** → nama `digispacekom-server` → **Save tunnel**.
3. Di halaman *Install and run connector*, pilih **Docker**. Akan muncul perintah seperti
   `docker run cloudflare/cloudflared:latest tunnel --no-autoupdate run --token eyJhIjoi...`
   **Salin hanya teks token** (mulai `eyJ...` sampai akhir). Jangan jalankan perintah itu.
4. Tempel token ke `.env`:

   ```bash
   nano .env
   # CLOUDFLARE_TUNNEL_TOKEN=eyJhIjoi....
   ```

5. Jalankan ulang; container `cloudflared` ikut menyala otomatis:

   ```bash
   ./prod.sh up -d
   ./prod.sh logs --tail 20 cloudflared     # cari "Registered tunnel connection"
   ```

   Di dashboard, status tunnel berubah menjadi **HEALTHY**. Klik **Next**.
6. Tab **Public Hostname → Add a public hostname**:

   | Kolom | Isi |
   |---|---|
   | Subdomain | *(kosong)* |
   | Domain | `digispacekom.net` |
   | Type | `HTTP` |
   | URL | `web:80` |

   Simpan. Ulangi untuk **Subdomain** `www` dengan URL yang sama.

   > Jika muncul *"A record with that host already exists"*, ada record lama. Buka **DNS → Records** di dashboard
   > domain, hapus record `A`/`CNAME` lama untuk `digispacekom.net` / `www`, dan jika hostname itu dipakai tunnel lama,
   > hapus juga dari tunnel lama (Zero Trust → Tunnels → tunnel lama → Public Hostname). Lalu ulangi.

7. Buka **https://digispacekom.net** dari HP (pakai data seluler, bukan WiFi kantor).

---

## 6. Pengaturan Cloudflare

Di dashboard domain `digispacekom.net`:

| Menu | Pengaturan |
|---|---|
| **SSL/TLS → Overview** | Mode **Full** |
| **SSL/TLS → Edge Certificates** | **Always Use HTTPS**: On · **Automatic HTTPS Rewrites**: On |
| **Caching → Configuration** | **Browser Cache TTL**: *Respect Existing Headers* |
| **Rules → Redirect Rules** | `www.digispacekom.net/*` → `https://digispacekom.net/${1}` (301), agar hanya satu alamat |

File CSS, JS, dan gambar sudah dikirim dengan header cache 30 hari, sehingga otomatis disimpan Cloudflare.

**Lindungi `/admin` (disarankan):** CMS punya login dan pembatasan percobaan, tetapi alamat `/admin` terbuka untuk
umum. Tambahkan lapisan kedua lewat **Cloudflare Zero Trust → Access → Applications → Add → Self-hosted**:
domain `digispacekom.net`, path `admin`, kebijakan *Allow* hanya untuk email staf. Akses ke CMS lalu butuh kode
dari email sebelum halaman login muncul. Gratis hingga 50 pengguna.

**Redirect domain lama** (bila `digispacekomp.net` masih dikelola klien dan sudah di Cloudflare): buat Redirect Rule
di domain lama → *All incoming requests* → *Dynamic* → `concat("https://digispacekom.net", http.request.uri.path)`,
status **301**.

---

## 7. Backup otomatis

Backup manual:

```bash
cd /opt/digispacekom
./prod.sh backup         # tersimpan di /opt/digispacekom/backups/, otomatis menyimpan 14 terakhir
```

Pastikan `cron` aktif, lalu jadwalkan setiap hari pukul 02.00:

```bash
systemctl is-active cron || sudo apt -y install cron
crontab -e
```

Tambahkan baris ini di paling bawah, simpan, keluar:

```cron
0 2 * * * /opt/digispacekom/prod.sh backup >> /opt/digispacekom/backups/cron.log 2>&1
```

> Bila akun Anda perlu `sudo` untuk Docker, pasang cron di akun root: `sudo crontab -e`.

> Backup yang hanya ada di server yang sama ikut hilang bila disknya rusak. Salin folder `backups/` ke
> Google Drive / flashdisk / folder jaringan secara berkala. Di CasaOS, folder ini bisa dibuka lewat aplikasi
> **Files** bila disalin ke `/DATA`. (Paket Pro sudah mencakup backup otomatis ke luar server.)

**Mengembalikan backup:**

```bash
cd /opt/digispacekom
gunzip -c backups/db-YYYYMMDD-HHMM.sql.gz | ./prod.sh exec -T db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" digispacekom'
```

---

## 8. Jalan otomatis setelah restart

Docker menyala otomatis saat server boot, dan semua container memakai `restart: unless-stopped`, jadi website
ikut hidup sendiri tanpa login dan tanpa langkah tambahan. Pastikan saja:

```bash
sudo systemctl is-enabled docker        # harus: enabled
```

**Uji wajib:** restart server (`sudo reboot`), jangan buka apa pun, tunggu 3–5 menit, lalu buka
https://digispacekom.net dari HP. Website harus tampil sendiri.

---

## 9. Cek akhir sebelum serah terima

- [ ] `https://digispacekom.net` terbuka dengan gembok (https), di HP dan laptop.
- [ ] Menu Layanan, Tentang & Klien, Artikel, Kontak, Cek Harga, dan Cek Status terbuka semua.
- [ ] `/servis-panggilan` dan `/it-support-kantor` (landing iklan) terbuka.
- [ ] Isi form booking → WhatsApp terbuka ke nomor yang benar dengan pesan terisi.
- [ ] Isi form *Minta penawaran* di halaman Kontak → WhatsApp terbuka.
- [ ] Tombol WhatsApp melayang mengarah ke nomor yang benar.
- [ ] Halaman yang tidak ada (mis. `/abc`) menampilkan "404 Not Found" tanpa detail error.
- [ ] **Uji restart server** (langkah 8) lulus: website kembali online sendiri.
- [ ] Cron backup terpasang (`crontab -l`) dan `./prod.sh backup` berhasil.
- [ ] Dashboard CasaOS tetap normal dan tidak ada aplikasi CasaOS lain yang berhenti.
- [ ] Uji kecepatan di **pagespeed.web.dev** (versi HP), simpan hasilnya untuk laporan sebelum/sesudah.
- [ ] Daftarkan `https://digispacekom.net/sitemap.xml` di **Google Search Console**.

- [ ] Login di `/admin` berhasil, dan halaman `/admin` tidak muncul di Google (sudah `noindex`).

**Konten contoh yang perlu diganti sebelum tayang.** Sebagian besar kini diubah lewat CMS (`/admin`), tanpa
menyentuh kode:

| Konten | Cara mengubah |
|---|---|
| Nama perusahaan, telepon, WhatsApp, email, alamat, jam buka | CMS → **Profil & kontak** |
| Layanan (6 halaman layanan), tab beranda | CMS → **Layanan**, **Tab beranda** |
| Klien, wilayah layanan, langkah layanan | CMS → **Klien & logo** dan **Wilayah & langkah** |
| Dua landing page iklan | CMS → **Landing page iklan** |
| Daftar estimasi harga | CMS → **Daftar harga** (hapus/ubah contoh bawaan) |
| Artikel | CMS → **Artikel** (hapus 3 artikel contoh) |
| Tiket contoh `DSK-1028` | CMS → **Tiket servis** (hapus) |
| Janji layanan "garansi 30 hari" yang tertulis di teks layanan dan landing | CMS → Layanan dan Landing page iklan |
| Logo (masih tiruan) | Perlu developer: `src/resources/views/partials/logo.blade.php`, `src/public/favicon.svg` |

Seeder hanya dijalankan otomatis saat database **masih kosong** (instalasi pertama).

---

## 10. Perawatan

Semua perintah dijalankan di terminal server, dari `/opt/digispacekom`.

| Keperluan | Perintah |
|---|---|
| Status container | `./prod.sh ps` |
| Log aplikasi | `./prod.sh logs -f app` |
| Log Nginx | `./prod.sh logs -f web` |
| Log tunnel | `./prod.sh logs -f cloudflared` |
| Restart semua | `./prod.sh restart` |
| Matikan | `./prod.sh down` *(data tetap aman)* |
| Nyalakan | `./prod.sh up -d` |
| Lihat booking yang masuk | CMS → **Permintaan masuk** (bisa diunduh sebagai CSV) |
| Tambah admin atau staf | CMS → **Pengguna & peran**, atau `./prod.sh exec app php artisan admin:create` (admin penuh) |
| Lupa password admin (semua admin terkunci) | `./prod.sh exec app php artisan tinker`, lalu `App\Models\User::where('email','…')->first()->update(['password'=>'password-baru-minimal-10'])` |

**Update website** setelah ada perubahan kode:

```bash
cd /opt/digispacekom
./prod.sh backup                 # selalu backup dulu
git pull
./prod.sh up -d --build
./prod.sh logs -f app            # tunggu "ready to handle connections"
```

Migrasi database dan cache dijalankan otomatis saat container menyala.

> **Jangan** menjalankan `./prod.sh down -v`: opsi `-v` **menghapus database**.

---

## 11. Jika ada masalah

| Gejala | Penyebab & solusi |
|---|---|
| `permission denied` saat menjalankan `docker` | Akun belum di grup Docker: `sudo usermod -aG docker $USER`, logout-login. Atau awali perintah dengan `sudo`. |
| `unknown flag` / error `!override` atau `!reset` di compose | Versi Docker Compose terlalu lama. Pasang versi terbaru (langkah 1). |
| `DB_PASSWORD belum diisi di file .env` | `.env` belum dibuat/diisi (langkah 3). Perintah harus dijalankan dari `/opt/digispacekom`. |
| `502 Bad Gateway` setelah baru dinyalakan | App masih memasang Composer / menunggu database. Tunggu, cek `./prod.sh logs -f app`. |
| Log app terus `waiting for database...` | Cek `./prod.sh logs db`. Bila password di `.env` diubah setelah instalasi pertama, kembalikan ke password lama (lihat di bawah). |
| `port is already allocated` | Port 8080 dipakai aplikasi lain. Ganti `WEB_PORT` di `.env`, lalu `./prod.sh up -d`. |
| Tunnel tidak **HEALTHY** | `./prod.sh logs cloudflared`. Pastikan token disalin lengkap tanpa spasi dan server punya internet. |
| Domain menampilkan error 1033 | Container `cloudflared` mati. Jalankan `./prod.sh up -d`. |
| Domain menampilkan 502 dari Cloudflare | Public hostname salah. URL harus `web:80` dengan type `HTTP`. |
| Domain masih menampilkan WordPress lama | Hostname `digispacekom.net` masih terdaftar di tunnel lama. Hapus dari tunnel lama (langkah 5), lalu Caching → Purge Everything. |
| Tampilan berantakan / CSS tidak muncul | Cloudflare: **Caching → Purge Everything**. |
| Form booking error **419** | Halaman dibuka terlalu lama (sesi habis). Muat ulang halaman lalu kirim lagi. |
| Website mati setelah server restart | `sudo systemctl is-enabled docker` harus `enabled`; `docker ps` harus menampilkan 4 container; bila tidak, jalankan `./prod.sh up -d`. |

**Mengganti password database setelah instalasi:**

```bash
./prod.sh exec db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" -e "ALTER USER '\''digispacekom'\''@'\''%'\'' IDENTIFIED BY '\''PASSWORD_BARU'\''"'
nano .env                 # ubah DB_PASSWORD=PASSWORD_BARU
./prod.sh up -d
```

Untuk masalah lain, kirim hasil `./prod.sh ps` dan `./prod.sh logs --tail 100 app` ke developer
(periksa dulu agar tidak ada password di dalamnya).

---

## 12. Catatan tentang dashboard CasaOS

- Setelah dijalankan, container website (`digispacekom-app-1`, `-web-1`, `-db-1`, `-cloudflared-1`) akan
  muncul di dashboard CasaOS sebagai aplikasi "legacy". Itu normal.
- **Kelola website lewat terminal (`prod.sh`), bukan lewat dashboard.** Jangan klik *Uninstall/Delete* pada
  container itu di dashboard. Pada pilihan yang menawarkan penghapusan data/volume, database ikut hilang.
- Dashboard CasaOS memakai port 80. Website memakai port 8080 dan hanya terbuka di server itu sendiri
  (`127.0.0.1`), sehingga tidak bentrok dan tidak terlihat dari jaringan kantor.
- Pembaruan CasaOS atau Docker tidak menghapus website: data ada di volume Docker dan di folder
  `/opt/digispacekom`.
