# Panduan Instalasi di Server Klien (Windows 11)

Website PT Digital Space Komputer · Laravel 13 · PHP 8.5 · Nginx · MariaDB 11.4 · Docker

Panduan ini memasang website di **PC/server Windows 11** milik klien sampai bisa diakses di
**https://digispacekom.net** lewat Cloudflare Tunnel. Tidak perlu IP statis dan tidak perlu
membuka port router.

```
Pengunjung ──https──> Cloudflare (CDN, SSL) ──tunnel──> cloudflared ──> Nginx ──> PHP-FPM (Laravel) ──> MariaDB
                                                         └──── semua di Docker (WSL2) di PC klien ────┘
```

Perkiraan waktu: 60–90 menit (sebagian besar menunggu unduhan dan restart).

> Server Linux (Ubuntu) lebih stabil untuk dipakai 24 jam. Jika klien punya pilihan, lihat
> [catatan Linux](#catatan-jika-servernya-linux) di akhir. Panduan utama di bawah untuk Windows 11.

---

## Daftar isi

1. [Persiapan](#1-persiapan)
2. [Atur Windows agar bisa jalan 24 jam](#2-atur-windows-agar-bisa-jalan-24-jam)
3. [Pasang WSL2 dan Ubuntu](#3-pasang-wsl2-dan-ubuntu)
4. [Pasang Docker Desktop](#4-pasang-docker-desktop)
5. [Ambil kode website](#5-ambil-kode-website)
6. [Isi konfigurasi `.env`](#6-isi-konfigurasi-env)
7. [Jalankan website](#7-jalankan-website)
8. [Hubungkan ke domain dengan Cloudflare Tunnel](#8-hubungkan-ke-domain-dengan-cloudflare-tunnel)
9. [Pengaturan Cloudflare](#9-pengaturan-cloudflare)
10. [Jalan otomatis setelah restart](#10-jalan-otomatis-setelah-restart)
11. [Backup otomatis](#11-backup-otomatis)
12. [Cek akhir sebelum serah terima](#12-cek-akhir-sebelum-serah-terima)
13. [Perawatan: update, restart, log](#13-perawatan)
14. [Jika ada masalah](#14-jika-ada-masalah)

**Cara membaca perintah:** kotak bertanda **PowerShell (Admin)** dijalankan di Windows PowerShell yang dibuka
dengan klik kanan → *Run as administrator*. Kotak bertanda **Ubuntu** dijalankan di jendela *Ubuntu*
(dari Start Menu). Jangan tertukar.

---

## 1. Persiapan

**Spesifikasi minimum PC/server**

| | Minimum | Disarankan |
|---|---|---|
| Windows | Windows 11 (Home/Pro) 64-bit, sudah di-update | Windows 11 Pro |
| RAM | 4 GB | 8 GB |
| Penyimpanan kosong | 20 GB | 40 GB SSD |
| Internet | Upload 10 Mbps | Upload 20 Mbps+ |

**Yang perlu disiapkan**

- [ ] Akses ke PC (langsung, atau AnyDesk/RDP). Gunakan akun Windows khusus, jangan minta password klien lewat chat.
- [ ] **Virtualisasi aktif.** Buka *Task Manager* → tab *Performance* → *CPU*; pada "Virtualization" harus tertulis **Enabled**.
      Jika *Disabled*, aktifkan **Intel VT-x / AMD-V (SVM Mode)** di BIOS.
- [ ] Akses ke akun **Cloudflare** tempat domain `digispacekom.net` dikelola.
      Jika domain belum di Cloudflare, tambahkan dulu di dashboard Cloudflare lalu ganti nameserver di
      registrar domain (bisa butuh beberapa jam sampai aktif).
- [ ] Repo kode bersifat publik selama masa development: `https://github.com/ganova/digispacekom`
- [ ] Di BIOS PC: **Restore on AC Power Loss → Power On**, agar PC menyala sendiri setelah listrik padam.

---

## 2. Atur Windows agar bisa jalan 24 jam

**PowerShell (Admin)** — matikan sleep dan hibernate saat tersambung listrik:

```powershell
powercfg /change standby-timeout-ac 0
powercfg /change hibernate-timeout-ac 0
powercfg /h off
```

**Login otomatis** (Docker Desktop baru jalan setelah ada yang login, jadi PC harus login sendiri saat menyala):

1. Tekan `Win + R`, ketik `netplwiz`, Enter.
2. Hilangkan centang **Users must enter a user name and password to use this computer**, klik OK, lalu masukkan password akun.
3. Jika kotak centang itu tidak ada: *Settings → Accounts → Sign-in options* → matikan
   **For improved security, only allow Windows Hello sign-in for Microsoft accounts**, lalu ulangi langkah 1.

> Konsekuensinya, siapa pun yang menyalakan PC langsung masuk ke Windows. Taruh PC di ruangan yang aman.

**Windows Update:** *Settings → Windows Update → Advanced options → Active hours* → atur jam kantor, supaya
restart otomatis terjadi di malam hari. Website akan kembali online sendiri sesudahnya (lihat langkah 10).

---

## 3. Pasang WSL2 dan Ubuntu

Docker di Windows berjalan di atas WSL2 (Linux ringan di dalam Windows).

**PowerShell (Admin):**

```powershell
wsl --install -d Ubuntu-24.04
```

Restart PC bila diminta. Setelah menyala, jendela **Ubuntu** terbuka sendiri dan meminta dibuatkan
*username* dan *password* Linux. Isi bebas, lalu catat.

Cek hasilnya (**PowerShell**):

```powershell
wsl -l -v          # Ubuntu-24.04 harus berstatus Running, VERSION 2
```

*(Opsional, untuk PC RAM 4 GB)* batasi pemakaian memori WSL supaya Windows tidak melambat. Buat file
`C:\Users\<nama-akun>\.wslconfig` berisi:

```ini
[wsl2]
memory=2GB
swap=2GB
```

Lalu `wsl --shutdown` dan buka Ubuntu lagi.

Di jendela **Ubuntu**, perbarui paket dasar:

```bash
sudo apt update && sudo apt -y upgrade
sudo apt -y install git curl ca-certificates
```

---

## 4. Pasang Docker Desktop

1. Unduh **Docker Desktop for Windows** dari docker.com, lalu pasang. Pilih opsi **Use WSL 2 instead of Hyper-V**.
   (Docker Desktop gratis untuk perusahaan di bawah 250 karyawan dan omzet US$10 juta/tahun.)
2. Buka Docker Desktop, setujui ketentuan (tidak perlu membuat akun, bisa *Skip*).
3. **Settings → General:** centang **Start Docker Desktop when you sign in to your computer**.
4. **Settings → Resources → WSL integration:** nyalakan **Enable integration with my default WSL distro** dan
   aktifkan untuk **Ubuntu-24.04**. Klik **Apply & restart**.

Cek di jendela **Ubuntu**:

```bash
docker --version
docker compose version        # harus versi 2.24 ke atas
docker run --rm hello-world   # harus muncul "Hello from Docker!"
```

---

## 5. Ambil kode website

Di jendela **Ubuntu**. Simpan project **di dalam Ubuntu** (`/opt/...`), **jangan** di `C:\` atau `/mnt/c/...`,
karena di drive Windows website jauh lebih lambat dan file skrip bisa rusak.

```bash
sudo mkdir -p /opt/digispacekom && sudo chown $USER: /opt/digispacekom
git clone https://github.com/ganova/digispacekom.git /opt/digispacekom
cd /opt/digispacekom
git log --oneline | head -3
```

> Tidak perlu login GitHub karena repo publik. Jika nanti repo dijadikan private, pakai *deploy key*
> (GitHub → repo → Settings → Deploy keys, hanya izin baca) atau `gh auth login`.

---

## 6. Isi konfigurasi `.env`

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

CLOUDFLARE_TUNNEL_TOKEN=          # dikosongkan dulu, diisi di langkah 8
```

Simpan: `Ctrl+O`, `Enter`, keluar dengan `Ctrl+X`. Amankan filenya:

```bash
chmod 600 .env
```

> `.env` **tidak pernah** boleh di-commit ke GitHub (sudah diblok oleh `.gitignore`), karena repo ini publik.
> Simpan isinya di password manager klien. Jika `DB_PASSWORD` kosong, website sengaja menolak menyala.
> Password database ditentukan saat pertama kali jalan; mengubahnya sesudahnya butuh langkah khusus (lihat [14](#14-jika-ada-masalah)).

---

## 7. Jalankan website

```bash
./prod.sh up -d --build
```

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

**Uji dari PC itu sendiri:**

```bash
curl -s -o /dev/null -w "%{http_code}\n" http://127.0.0.1:8080/    # harus 200
./prod.sh ps                                                        # app, web, db = running
```

Lalu buka **http://localhost:8080** di browser Windows. Website harus tampil.

---

## 8. Hubungkan ke domain dengan Cloudflare Tunnel

> Jika `digispacekom.net` saat ini mengarah ke WordPress lama, langkah ini **menggantikannya** dengan website baru.
> Backup WordPress lama dulu bila masih diperlukan.

1. Login ke **dash.cloudflare.com** → menu kiri **Zero Trust** (pertama kali: pilih paket **Free**).
2. **Networks → Tunnels → Create a tunnel** → pilih **Cloudflared** → nama `digispacekom-server` → **Save tunnel**.
3. Di halaman *Install and run connector*, pilih **Docker**. Akan muncul perintah seperti
   `docker run cloudflare/cloudflared:latest tunnel --no-autoupdate run --token eyJhIjoi...`
   **Salin hanya teks token** (mulai `eyJ...` sampai akhir). Jangan jalankan perintah itu.
4. Tempel token ke `.env` (jendela **Ubuntu**):

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

   > Jika muncul *"A record with that host already exists"*, buka **DNS → Records** di dashboard domain,
   > hapus record `A`/`CNAME` lama untuk `digispacekom.net` / `www`, lalu ulangi.

7. Buka **https://digispacekom.net** dari HP (pakai data seluler, bukan WiFi kantor).

---

## 9. Pengaturan Cloudflare

Di dashboard domain `digispacekom.net`:

| Menu | Pengaturan |
|---|---|
| **SSL/TLS → Overview** | Mode **Full** |
| **SSL/TLS → Edge Certificates** | **Always Use HTTPS**: On · **Automatic HTTPS Rewrites**: On |
| **Caching → Configuration** | **Browser Cache TTL**: *Respect Existing Headers* |
| **Rules → Redirect Rules** | `www.digispacekom.net/*` → `https://digispacekom.net/${1}` (301), agar hanya satu alamat |

File CSS, JS, dan gambar sudah dikirim dengan header cache 30 hari, sehingga otomatis disimpan Cloudflare.

**Redirect domain lama** (bila `digispacekomp.net` masih dikelola klien dan sudah di Cloudflare): buat Redirect Rule
di domain lama → *All incoming requests* → *Dynamic* → `concat("https://digispacekom.net", http.request.uri.path)`,
status **301**.

---

## 10. Jalan otomatis setelah restart

Agar website kembali online sendiri setelah listrik padam atau Windows Update. Rantainya:
PC menyala → login otomatis → Docker Desktop menyala → container menyala (`restart: unless-stopped`).

Satu tugas tambahan menjaga Ubuntu tetap hidup di latar belakang, supaya Docker bisa membaca folder project
sesudah restart.

**PowerShell (Admin):**

```powershell
schtasks /Create /TN "DSK WSL KeepAlive" /SC ONLOGON /RL HIGHEST /F `
  /TR "conhost.exe --headless wsl.exe -d Ubuntu-24.04 --exec sleep infinity"
```

Jalankan sekarang tanpa menunggu restart, lalu cek:

```powershell
schtasks /Run /TN "DSK WSL KeepAlive"
schtasks /Query /TN "DSK WSL KeepAlive"
```

**Uji wajib:** restart PC (`Start → Power → Restart`), jangan buka apa pun, tunggu 3–5 menit, lalu buka
https://digispacekom.net dari HP. Website harus tampil sendiri. Jika tidak, lihat [bagian 14](#14-jika-ada-masalah).

---

## 11. Backup otomatis

Backup manual (jendela **Ubuntu**):

```bash
cd /opt/digispacekom
./prod.sh backup         # tersimpan di /opt/digispacekom/backups/, otomatis menyimpan 14 terakhir
```

Jadwalkan setiap hari pukul 02.00. **PowerShell (Admin):**

```powershell
schtasks /Create /TN "DSK Backup Database" /SC DAILY /ST 02:00 /F `
  /TR "conhost.exe --headless wsl.exe -d Ubuntu-24.04 -- /opt/digispacekom/prod.sh backup"
```

Folder backup bisa dibuka dari Windows Explorer di alamat:

```
\\wsl.localhost\Ubuntu-24.04\opt\digispacekom\backups
```

> Backup yang hanya ada di PC yang sama ikut hilang bila disknya rusak. Salin folder ini ke Google Drive / flashdisk
> secara berkala (atau gunakan Paket Pro yang sudah mencakup backup ke luar server).

**Mengembalikan backup** (jendela **Ubuntu**):

```bash
cd /opt/digispacekom
gunzip -c backups/db-YYYYMMDD-HHMM.sql.gz | ./prod.sh exec -T db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" digispacekom'
```

---

## 12. Cek akhir sebelum serah terima

- [ ] `https://digispacekom.net` terbuka dengan gembok (https), di HP dan laptop.
- [ ] Menu Layanan, Tentang & Klien, Artikel, Kontak, Cek Harga, dan Cek Status terbuka semua.
- [ ] `/servis-panggilan` dan `/it-support-kantor` (landing iklan) terbuka.
- [ ] Isi form booking → WhatsApp terbuka ke nomor yang benar dengan pesan terisi.
- [ ] Isi form *Minta penawaran* di halaman Kontak → WhatsApp terbuka.
- [ ] Tombol WhatsApp melayang mengarah ke nomor yang benar.
- [ ] Halaman yang tidak ada (mis. `/abc`) menampilkan "404 Not Found" tanpa detail error.
- [ ] **Uji restart PC** (langkah 10) lulus: website kembali online sendiri.
- [ ] Tugas terjadwal terpasang: `schtasks /Query /TN "DSK Backup Database"` dan `"DSK WSL KeepAlive"`.
- [ ] `./prod.sh backup` berhasil dan file muncul di folder backups.
- [ ] Uji kecepatan di **pagespeed.web.dev** (versi HP), simpan hasilnya untuk laporan sebelum/sesudah.
- [ ] Daftarkan `https://digispacekom.net/sitemap.xml` di **Google Search Console**.

**Konten contoh yang perlu diganti sebelum tayang:**

| Konten | Lokasi |
|---|---|
| Logo (masih tiruan) | `src/resources/views/partials/logo.blade.php`, `src/public/favicon.svg` |
| Data perusahaan, layanan, klien, area, jam buka, email | `src/config/site.php` |
| Daftar estimasi harga | `src/database/seeders/PriceItemSeeder.php` |
| Artikel | `src/database/seeders/ArticleSeeder.php` |
| Tiket contoh `DSK-1028` | `src/database/seeders/ServiceTicketSeeder.php` |
| Janji layanan (garansi 30 hari, dsb.) | `src/config/site.php` dan teks di `src/resources/views/` |

Seeder hanya dijalankan otomatis saat database **masih kosong** (instalasi pertama). Setelah itu, ubah data
lewat database atau minta developer.

---

## 13. Perawatan

Semua perintah dijalankan di jendela **Ubuntu**, dari `/opt/digispacekom`.

| Keperluan | Perintah |
|---|---|
| Status container | `./prod.sh ps` |
| Log aplikasi | `./prod.sh logs -f app` |
| Log Nginx | `./prod.sh logs -f web` |
| Log tunnel | `./prod.sh logs -f cloudflared` |
| Restart semua | `./prod.sh restart` |
| Matikan | `./prod.sh down` *(data tetap aman)* |
| Nyalakan | `./prod.sh up -d` |
| Lihat booking yang masuk | `./prod.sh exec db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" digispacekom -e "select id,type,name,device,location,created_at from bookings order by id desc limit 20"'` |

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

## 14. Jika ada masalah

| Gejala | Penyebab & solusi |
|---|---|
| `docker: command not found` di Ubuntu | Integrasi WSL belum aktif: Docker Desktop → Settings → Resources → WSL integration → aktifkan Ubuntu-24.04. |
| Docker Desktop macet di "Starting…" | **PowerShell (Admin):** `wsl --update` lalu `wsl --shutdown`, buka Docker Desktop lagi. Pastikan virtualisasi aktif di BIOS. |
| `./prod.sh: bad interpreter` / `\r` | Project tidak di dalam Ubuntu atau di-clone dari Windows. Hapus, lalu clone ulang dari jendela Ubuntu ke `/opt/digispacekom`. |
| Website sangat lambat | Project berada di `/mnt/c/...`. Pindahkan ke `/opt/digispacekom` (langkah 5). |
| `DB_PASSWORD belum diisi di file .env` | `.env` belum dibuat/diisi (langkah 6). Perintah harus dijalankan dari `/opt/digispacekom`. |
| `502 Bad Gateway` setelah baru dinyalakan | App masih memasang Composer / menunggu database. Tunggu, cek `./prod.sh logs -f app`. |
| Log app terus `waiting for database...` | Cek `./prod.sh logs db`. Bila password di `.env` diubah setelah instalasi pertama, kembalikan ke password lama (lihat di bawah). |
| `port is already allocated` | Port 8080 dipakai aplikasi lain. Ganti `WEB_PORT` di `.env`, lalu `./prod.sh up -d`. |
| Tunnel tidak **HEALTHY** | `./prod.sh logs cloudflared`. Pastikan token disalin lengkap tanpa spasi dan PC punya internet. |
| Domain menampilkan error 1033 | Container `cloudflared` mati. Jalankan `./prod.sh up -d`. |
| Domain menampilkan 502 dari Cloudflare | Public hostname salah. URL harus `web:80` dengan type `HTTP`. |
| Tampilan berantakan / CSS tidak muncul | Cloudflare: **Caching → Purge Everything**. |
| Form booking error **419** | Halaman dibuka terlalu lama (sesi habis). Muat ulang halaman lalu kirim lagi. |
| **Website mati setelah PC restart** | Periksa berurutan: (1) PC login otomatis? (2) Docker Desktop menyala di system tray? (3) `docker ps` di Ubuntu menampilkan 4 container? (4) tugas "DSK WSL KeepAlive" ada? Jika container tidak ada, `./prod.sh up -d`. |

**Mengganti password database setelah instalasi:**

```bash
./prod.sh exec db sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" -e "ALTER USER '\''digispacekom'\''@'\''%'\'' IDENTIFIED BY '\''PASSWORD_BARU'\''"'
nano .env                 # ubah DB_PASSWORD=PASSWORD_BARU
./prod.sh up -d
```

Untuk masalah lain, kirim hasil `./prod.sh ps` dan `./prod.sh logs --tail 100 app` ke developer
(periksa dulu agar tidak ada password di dalamnya).

---

## Catatan jika servernya Linux

Langkah 2–4 diganti satu blok pemasangan Docker langsung (Ubuntu 22.04/24.04), sisanya sama:

```bash
sudo apt update && sudo apt -y install git curl ca-certificates
curl -fsSL https://get.docker.com | sudo sh
sudo usermod -aG docker $USER      # logout lalu login lagi
sudo systemctl enable --now docker
```

Lalu lanjut ke langkah 5. Untuk jadwal backup, pakai `crontab -e`:

```cron
0 2 * * * /opt/digispacekom/prod.sh backup >> /opt/digispacekom/backups/cron.log 2>&1
```

Container otomatis menyala saat server boot, tanpa login otomatis dan tanpa tugas KeepAlive.
