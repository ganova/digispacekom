<?php

/*
|--------------------------------------------------------------------------
| Konten situs Digital Space Komputer
|--------------------------------------------------------------------------
| Data perusahaan, layanan, segmen, klien, dan landing page iklan.
| Teks, estimasi, dan janji layanan (mis. garansi 30 hari) masih contoh
| dan perlu disesuaikan dengan kebijakan klien sebelum tayang.
*/

return [

    // akar folder gambar unggahan CMS; null = public/uploads (hanya diubah oleh tes)
    'upload_root' => null,

    // kategori di halaman Cek Harga; dapat ditambah lewat CMS
    'price_categories' => [
        'laptop' => 'Laptop',
        'pc' => 'PC',
        'macbook' => 'MacBook',
        'printer' => 'Printer',
        'jaringan' => 'Jaringan',
        'sparepart' => 'Sparepart',
    ],

    'name' => 'PT Digital Space Komputer',
    'short' => 'Digital Space Komputer',
    'tagline' => 'Mitra IT untuk kantor, sekolah, dan instansi',
    'phone' => env('SITE_PHONE', '0813-8502-8980'),
    'whatsapp' => env('SITE_WHATSAPP', '6281385028980'),
    'email' => 'info@digispacekom.net',
    'city' => 'Bekasi',
    'address' => 'Bekasi, Jawa Barat',
    'area' => 'Jabodetabek',
    'hours' => 'Senin–Sabtu 08.00–20.00',
    'maps_query' => 'PT Digital Space Komputer Bekasi',

    'clients' => [
        'Embassy of India Jakarta',
        'KPU Jakarta Pusat',
        'Wholesale Electric Asia',
        'Golfzon',
        'SMA Negeri 88 Jakarta',
    ],

    // Enam layanan; masing-masing punya halaman detail (Paket Bisnis)
    'services' => [
        'service-laptop-pc' => [
            'title' => 'Service Laptop & PC',
            'icon' => 'laptop',
            'short' => 'Mati total, lambat, layar, keyboard, instal ulang.',
            'price_category' => 'laptop',
            'intro' => 'Laptop dan PC kantor maupun rumah yang bermasalah kami cek langsung di tempat. Sebagian besar kerusakan ringan selesai saat itu juga; yang perlu dibawa ke workshop diberi tanda terima dan nomor tiket.',
            'problems' => [
                'Mati total atau tidak mau menyala',
                'Lambat, sering hang, atau blue screen',
                'Layar bergaris, redup, atau pecah',
                'Keyboard dan touchpad tidak berfungsi',
                'Instal ulang Windows dan backup data',
                'Terkena air atau tumpahan minuman',
            ],
        ],
        'macbook-imac' => [
            'title' => 'MacBook & iMac',
            'icon' => 'monitor',
            'short' => 'Diagnosa, ganti baterai & SSD, perbaikan board.',
            'price_category' => 'macbook',
            'intro' => 'Penanganan MacBook dan iMac oleh teknisi yang terbiasa dengan perangkat Apple, dari ganti baterai sampai perbaikan logic board.',
            'problems' => [
                'Baterai cepat habis atau "Service Recommended"',
                'Upgrade atau ganti SSD',
                'MacBook tidak menyala setelah kena air',
                'Instal ulang macOS dan migrasi data',
                'Keyboard butterfly macet',
                'Perbaikan logic board',
            ],
        ],
        'service-printer' => [
            'title' => 'Service Printer',
            'icon' => 'printer',
            'short' => 'Tinta macet, paper jam, head cleaning, reset.',
            'price_category' => 'printer',
            'intro' => 'Printer inkjet, laser, dan dot matrix untuk kantor dan rumah. Teknisi membawa alat cleaning dan sparepart umum sehingga kebanyakan selesai di tempat.',
            'problems' => [
                'Paper jam atau kertas tidak tertarik',
                'Hasil cetak bergaris atau warna hilang',
                'Tinta macet, head cleaning',
                'Reset printer (blinking / waste ink penuh)',
                'Instal infus tinta',
                'Printer jaringan tidak terdeteksi',
            ],
        ],
        'server-jaringan' => [
            'title' => 'Server & Jaringan',
            'icon' => 'server',
            'short' => 'Instalasi LAN/WiFi kantor, server, dan CCTV.',
            'price_category' => 'jaringan',
            'intro' => 'Perancangan dan instalasi jaringan kantor, sekolah, dan instansi: kabel LAN, WiFi, server file, sampai CCTV, termasuk perawatan berkala.',
            'problems' => [
                'Instalasi kabel LAN dan rak server',
                'WiFi kantor lambat atau tidak menjangkau semua ruangan',
                'Setting router, mikrotik, dan pembagian bandwidth',
                'Server file dan sharing printer',
                'Instalasi dan perawatan CCTV',
                'Perawatan rutin jaringan kantor',
            ],
        ],
        'upgrade-rakit-pc' => [
            'title' => 'Upgrade & Rakit PC',
            'icon' => 'chip',
            'short' => 'Tambah RAM/SSD, rakit PC kerja dan gaming.',
            'price_category' => 'pc',
            'intro' => 'Perangkat lama dibuat lebih cepat dengan upgrade yang tepat, atau rakit PC baru sesuai kebutuhan kerja, desain, dan gaming.',
            'problems' => [
                'Tambah RAM dan ganti HDD ke SSD',
                'Pindah data ke SSD baru tanpa instal ulang',
                'Rakit PC kantor, desain, dan gaming',
                'Ganti power supply dan pendingin',
                'Pasang kartu grafis',
                'Konsultasi spesifikasi sesuai anggaran',
            ],
        ],
        'pengadaan-perangkat' => [
            'title' => 'Pengadaan Perangkat',
            'icon' => 'box',
            'short' => 'Laptop, PC, dan sparepart untuk kantor.',
            'price_category' => null,
            'intro' => 'Pengadaan laptop, PC, printer, perangkat jaringan, dan sparepart untuk kantor dan instansi, lengkap dengan instalasi dan dokumen penawaran resmi.',
            'problems' => [
                'Laptop dan PC kantor dalam jumlah banyak',
                'Printer dan perangkat jaringan',
                'Sparepart dan aksesori',
                'Lisensi software',
                'Instalasi dan setting siap pakai',
                'Penawaran resmi untuk instansi',
            ],
        ],
    ],

    // Tab segmen di beranda (Konsep B)
    'segments' => [
        'kantor' => [
            'label' => 'Kantor & instansi',
            'services' => ['service-laptop-pc', 'macbook-imac', 'service-printer', 'server-jaringan', 'upgrade-rakit-pc', 'pengadaan-perangkat'],
        ],
        'rumah' => [
            'label' => 'Rumah',
            'services' => ['service-laptop-pc', 'macbook-imac', 'service-printer', 'upgrade-rakit-pc'],
        ],
        'sekolah' => [
            'label' => 'Sekolah',
            'services' => ['server-jaringan', 'service-laptop-pc', 'service-printer', 'pengadaan-perangkat'],
        ],
    ],

    // Dua landing page iklan (Konsep A) — Paket Bisnis
    'landings' => [
        'servis-panggilan' => [
            'meta_title' => 'Service Laptop & PC Panggilan Jabodetabek',
            'meta_description' => 'Teknisi datang ke rumah atau kantor. Service laptop, PC, MacBook, dan printer se-Jabodetabek. Biaya disepakati dulu, garansi 30 hari.',
            'headline' => 'Laptop atau PC bermasalah?',
            'highlight' => 'Teknisi kami yang datang.',
            'lead' => 'Service laptop, PC, MacBook, dan printer di rumah atau kantor kakak, untuk seluruh Jabodetabek.',
            'checks' => ['Cek & diagnosa di tempat', 'Biaya disepakati sebelum dikerjakan', 'Garansi servis 30 hari'],
            'form_title' => 'Booking teknisi',
            'devices' => ['Laptop', 'PC', 'MacBook', 'Printer', 'Jaringan'],
            'placeholder' => 'Contoh: laptop mati total setelah kena air',
        ],
        'it-support-kantor' => [
            'meta_title' => 'IT Support & Maintenance Kantor Jabodetabek',
            'meta_description' => 'Kontrak maintenance komputer, printer, dan jaringan untuk kantor, sekolah, dan instansi. Teknisi datang, laporan aset IT tiap bulan.',
            'headline' => 'Komputer kantor sering bermasalah?',
            'highlight' => 'Serahkan perawatannya ke kami.',
            'lead' => 'Kontrak maintenance laptop, PC, printer, dan jaringan untuk kantor, sekolah, dan instansi se-Jabodetabek.',
            'checks' => ['Kunjungan rutin & prioritas perbaikan', 'Laporan kondisi aset IT tiap bulan', 'Dipercaya kedutaan & instansi pemerintah'],
            'form_title' => 'Minta jadwal survei',
            'devices' => ['Laptop & PC', 'Printer', 'Jaringan', 'Server', 'CCTV'],
            'placeholder' => 'Contoh: 25 PC, 4 printer, WiFi 2 lantai',
        ],
    ],

    'steps' => [
        ['Booking', 'Isi form atau chat WhatsApp, pilih jadwal.'],
        ['Teknisi datang', 'Cek di tempat, biaya disepakati dulu.'],
        ['Selesai & bergaransi', 'Bayar setelah selesai, garansi 30 hari.'],
    ],

    'service_areas' => ['Bekasi', 'Jakarta Timur', 'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Utara', 'Jakarta Barat', 'Depok', 'Bogor', 'Tangerang'],

];
