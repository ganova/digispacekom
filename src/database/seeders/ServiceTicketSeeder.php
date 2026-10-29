<?php

namespace Database\Seeders;

use App\Models\ServiceTicket;
use Illuminate\Database\Seeder;

/** Tiket demo untuk halaman Cek Status Servis. */
class ServiceTicketSeeder extends Seeder
{
    public function run(): void
    {
        $ticket = ServiceTicket::create([
            'code' => 'DSK-1028',
            'customer_name' => 'Budi Santoso',
            'device' => 'Laptop ASUS VivoBook 14',
            'complaint' => 'Layar bergaris setelah jatuh',
        ]);

        $ticket->events()->createMany([
            ['title' => 'Diterima', 'occurred_at' => '2026-10-26 10:12', 'state' => 'done', 'sort' => 1],
            ['title' => 'Dicek teknisi', 'note' => 'Panel LCD rusak, perlu ganti', 'occurred_at' => '2026-10-26 14:30', 'state' => 'done', 'sort' => 2],
            ['title' => 'Menunggu sparepart', 'note' => 'Estimasi Rab, 28 Okt', 'state' => 'now', 'sort' => 3],
            ['title' => 'Siap diambil', 'state' => 'next', 'sort' => 4],
        ]);
    }
}
