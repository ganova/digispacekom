<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/** Simpan lead, lalu teruskan pengunjung ke WhatsApp dengan pesan yang sudah terisi. */
class BookingController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // honeypot: bot biasanya mengisi semua field
        if ($request->filled('website')) {
            return back();
        }

        $isQuote = $request->input('type') === 'penawaran';

        $data = $request->validate([
            'type' => ['nullable', 'in:booking,penawaran'],
            'source' => ['nullable', 'string', 'max:60'],
            'name' => [$isQuote ? 'required' : 'nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:150'],
            'device' => ['nullable', 'string', 'max:60'],
            'complaint' => ['required', 'string', 'max:1000'],
            'location' => [$isQuote ? 'nullable' : 'required', 'string', 'max:150'],
            'units' => ['nullable', 'integer', 'min:1', 'max:9999'],
        ], [], [
            'name' => 'nama',
            'complaint' => $isQuote ? 'kebutuhan' : 'keluhan',
            'location' => 'lokasi',
            'units' => 'jumlah unit',
        ]);

        $booking = Booking::create(['type' => $data['type'] ?? 'booking'] + $data);

        return redirect()->away(Site::whatsappUrl($this->message($booking)));
    }

    private function message(Booking $b): string
    {
        $isQuote = $b->type === 'penawaran';

        $lines = [$isQuote
            ? 'Halo Digital Space Komputer, saya ingin minta penawaran.'
            : 'Halo Digital Space Komputer, saya ingin booking teknisi.'];

        $fields = [
            'Nama' => $b->name,
            'Instansi' => $b->organization,
            'Perangkat' => $b->device,
            'Jumlah unit' => $b->units,
            $isQuote ? 'Kebutuhan' : 'Keluhan' => $b->complaint,
            'Lokasi' => $b->location,
        ];

        foreach (array_filter($fields) as $label => $value) {
            $lines[] = "{$label}: {$value}";
        }

        $lines[] = "(Ref #{$b->id})";

        return implode("\n", $lines);
    }
}
