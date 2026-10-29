<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;

/** Artikel contoh. Saat tayang diganti artikel yang dipindah dari web lama. */
class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        $articles = [
            [
                'slug' => 'tanda-laptop-perlu-ganti-ssd',
                'title' => '5 tanda laptop kantor sudah waktunya ganti SSD',
                'category' => 'Tips',
                'excerpt' => 'Laptop lama tidak selalu harus diganti. Sering kali cukup mengganti HDD ke SSD agar kembali cepat.',
                'body' => "<p>Laptop yang terasa lambat belum tentu harus diganti. Pada banyak laptop kantor berumur 3–5 tahun, penyebab utamanya adalah hard disk (HDD) yang sudah lambat atau mulai rusak.</p>\n<h2>Tanda-tandanya</h2>\n<ol><li>Butuh lebih dari 2 menit untuk masuk ke Windows.</li><li>Lampu indikator disk menyala terus walaupun tidak membuka aplikasi.</li><li>Membuka file Excel atau browser terasa tertahan.</li><li>Muncul bunyi klik dari dalam laptop.</li><li>Windows sering meminta perbaikan disk saat dinyalakan.</li></ol>\n<h2>Apa yang dikerjakan teknisi</h2>\n<p>Teknisi memindahkan seluruh data dan sistem ke SSD baru, sehingga tidak perlu instal ulang. Pekerjaan biasanya selesai di tempat dalam 1–2 jam.</p>",
                'published_at' => '2026-10-20 09:00',
            ],
            [
                'slug' => 'merawat-printer-kantor',
                'title' => 'Cara merawat printer kantor agar tidak sering macet',
                'category' => 'Tips',
                'excerpt' => 'Paper jam dan tinta macet bisa dicegah dengan beberapa kebiasaan kecil.',
                'body' => "<p>Printer kantor yang dipakai setiap hari paling sering bermasalah di dua hal: kertas macet dan tinta yang mengering.</p>\n<h2>Kebiasaan yang membantu</h2>\n<ul><li>Gunakan kertas yang tidak lembap dan jangan terlalu penuh di baki.</li><li>Cetak minimal satu halaman berwarna setiap minggu agar head tidak kering.</li><li>Bersihkan roller penarik kertas setiap bulan.</li><li>Matikan printer lewat tombol power, bukan mencabut listrik.</li></ul>\n<p>Untuk kantor dengan banyak printer, perawatan bisa dimasukkan ke dalam kontrak maintenance bulanan.</p>",
                'published_at' => '2026-10-12 09:00',
            ],
            [
                'slug' => 'wifi-kantor-lambat',
                'title' => 'WiFi kantor lambat padahal internet kencang? Ini penyebabnya',
                'category' => 'Jaringan',
                'excerpt' => 'Kecepatan dari provider sudah besar, tetapi WiFi di ruangan tetap lambat. Biasanya masalahnya ada di jaringan dalam kantor.',
                'body' => "<p>Banyak kantor sudah berlangganan internet dengan kecepatan besar, tetapi karyawan tetap mengeluh WiFi lambat. Penyebabnya sering ada di dalam kantor sendiri.</p>\n<h2>Penyebab umum</h2>\n<ul><li>Satu router untuk seluruh lantai, sehingga sinyal lemah di ruangan jauh.</li><li>Terlalu banyak perangkat pada satu access point.</li><li>Tidak ada pembagian bandwidth, sehingga satu pengguna bisa menghabiskan jalur.</li><li>Kabel LAN lama yang belum mendukung gigabit.</li></ul>\n<p>Survei jaringan membantu menentukan titik access point dan pengaturan yang tepat sebelum membeli perangkat baru.</p>",
                'published_at' => '2026-10-02 09:00',
            ],
        ];

        foreach ($articles as $article) {
            Article::create($article);
        }
    }
}
