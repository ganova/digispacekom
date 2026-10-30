<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Booking;
use App\Models\PriceItem;
use App\Models\ServiceTicket;
use App\Models\Setting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Artikel bawaan seeder; masih ada berarti konten contoh belum diganti. */
    private const SAMPLE_ARTICLES = ['tanda-laptop-perlu-ganti-ssd', 'merawat-printer-kantor', 'wifi-kantor-lambat'];

    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newBookings' => Booking::where('status', 'baru')->count(),
            'weekBookings' => Booking::where('created_at', '>=', now()->subDays(7))->count(),
            'tickets' => ServiceTicket::count(),
            'activeTickets' => ServiceTicket::whereHas('events', fn ($q) => $q->where('state', 'now'))->count(),
            'prices' => PriceItem::count(),
            'articles' => Article::count(),
            'drafts' => Article::whereNull('published_at')->count(),
            'latest' => Booking::latest()->limit(6)->get(),
            'inProgress' => ServiceTicket::whereHas('events', fn ($q) => $q->where('state', 'now'))
                ->with(['events' => fn ($q) => $q->where('state', 'now')])
                ->latest('updated_at')->limit(5)->get(),
            'chart' => $this->chart(),
            'todo' => $this->launchChecklist(),
        ]);
    }

    /** Jumlah permintaan per hari, 14 hari terakhir. */
    private function chart(): array
    {
        $start = now()->subDays(13)->startOfDay();

        $counts = Booking::where('created_at', '>=', $start)->pluck('created_at')
            ->countBy(fn ($date) => $date->format('Y-m-d'));

        return collect(range(0, 13))->map(function (int $i) use ($start, $counts) {
            $day = $start->copy()->addDays($i);

            return [
                'label' => $day->translatedFormat('j M'),
                'day' => $day->format('j'),
                'count' => $counts[$day->format('Y-m-d')] ?? 0,
                'today' => $day->isToday(),
            ];
        })->all();
    }

    /** Daftar periksa sebelum situs tayang: konten contoh yang perlu diganti. */
    private function launchChecklist(): array
    {
        return [
            [
                'done' => Setting::isCustomized('email', 'whatsapp'),
                'text' => 'Periksa profil & kontak',
                'hint' => 'Email info@digispacekom.net masih tebakan. Simpan sekali setelah dicek.',
                'url' => route('admin.profile'),
            ],
            [
                'done' => ! ServiceTicket::where('code', 'DSK-1028')->exists(),
                'text' => 'Hapus tiket contoh DSK-1028',
                'hint' => 'Tiket demo untuk halaman Cek Status.',
                'url' => route('admin.tickets.index', ['q' => 'DSK-1028']),
            ],
            [
                'done' => ! Article::whereIn('slug', self::SAMPLE_ARTICLES)->exists(),
                'text' => 'Ganti 3 artikel contoh',
                'hint' => 'Pindahkan artikel dari web lama atau tulis baru.',
                'url' => route('admin.articles.index'),
            ],
            [
                'done' => PriceItem::whereColumn('updated_at', '>', 'created_at')->exists(),
                'text' => 'Tinjau daftar harga',
                'hint' => 'Harga bawaan masih estimasi contoh.',
                'url' => route('admin.prices.index'),
            ],
            [
                'done' => Setting::isCustomized('services'),
                'text' => 'Periksa teks layanan & janji garansi',
                'hint' => 'Mis. “garansi 30 hari” harus sesuai kebijakan.',
                'url' => route('admin.services.index'),
            ],
        ];
    }
}
