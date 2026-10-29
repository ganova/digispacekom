<?php

namespace Database\Seeders;

use App\Models\PriceItem;
use Illuminate\Database\Seeder;

/** Estimasi contoh — sesuaikan dengan daftar harga resmi klien. */
class PriceItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // category, name, min, max, suffix, note, keywords
            ['laptop', 'Laptop mati total', 150_000, 450_000, null, 'Cek & diagnosa 1 hari', 'mati tidak nyala no power'],
            ['laptop', 'Instal ulang + backup data', 150_000, null, null, 'Selesai di tempat', 'install windows os lambat virus'],
            ['laptop', 'Ganti SSD + pindah data', 350_000, null, '+ SSD', 'Termasuk pemasangan', 'upgrade ssd hdd lambat'],
            ['laptop', 'Ganti layar laptop', 250_000, null, '+ panel', 'Pemasangan 1–2 jam', 'lcd layar bergaris pecah blank'],
            ['laptop', 'Ganti keyboard laptop', 150_000, null, '+ keyboard', 'Pemasangan di tempat', 'keyboard tombol rusak'],
            ['laptop', 'Laptop kena air', 250_000, 800_000, null, 'Pembersihan & cek board', 'air tumpah basah mati'],
            ['laptop', 'Cleaning & ganti thermal paste', 150_000, null, null, 'Selesai di tempat', 'panas overheat kipas berisik'],
            ['pc', 'PC tidak menyala', 150_000, 400_000, null, 'Cek power supply & board', 'mati psu tidak nyala'],
            ['pc', 'Tambah RAM', 75_000, null, '+ RAM', 'Termasuk pemasangan', 'upgrade memori lambat'],
            ['pc', 'Rakit PC (jasa)', 250_000, null, null, 'Termasuk instal Windows', 'rakit gaming kerja build'],
            ['macbook', 'Ganti baterai MacBook', 650_000, 1_500_000, null, 'Tergantung model', 'baterai battery service recommended'],
            ['macbook', 'Upgrade SSD MacBook', 450_000, null, '+ SSD', 'Model tertentu', 'ssd upgrade storage'],
            ['macbook', 'Instal ulang macOS', 200_000, null, null, 'Termasuk backup', 'install os lambat'],
            ['printer', 'Printer paper jam / tinta macet', 100_000, 250_000, null, 'Selesai di tempat', 'macet kertas tinta jam'],
            ['printer', 'Head cleaning & reset printer', 100_000, 200_000, null, 'Selesai di tempat', 'reset blinking bergaris'],
            ['printer', 'Pasang infus tinta', 150_000, null, '+ set infus', 'Selesai di tempat', 'infus tinta'],
            ['jaringan', 'Setting router & WiFi', 200_000, 500_000, null, 'Per titik lokasi', 'wifi router internet lambat'],
            ['jaringan', 'Tarik kabel LAN', 75_000, null, '/ titik', 'Belum termasuk material', 'lan kabel jaringan'],
            ['jaringan', 'Instalasi CCTV 4 kamera', 600_000, null, '+ perangkat', 'Termasuk setting HP', 'cctv kamera'],
        ];

        foreach ($items as $i => [$category, $name, $min, $max, $suffix, $note, $keywords]) {
            PriceItem::create([
                'category' => $category,
                'name' => $name,
                'price_min' => $min,
                'price_max' => $max,
                'price_suffix' => $suffix,
                'note' => $note,
                'keywords' => $keywords,
                'sort' => $i,
            ]);
        }
    }
}
