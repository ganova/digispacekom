<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.bookings.index', [
            'bookings' => $this->filtered($request)->latest()->paginate(20)->withQueryString(),
            'counts' => Booking::selectRaw('status, count(*) as n')->groupBy('status')->pluck('n', 'status'),
        ]);
    }

    public function show(Booking $booking): View
    {
        return view('admin.bookings.show', ['booking' => $booking]);
    }

    public function update(Request $request, Booking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', array_keys(Booking::STATUSES))],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->update($data);

        return back()->with('ok', 'Permintaan diperbarui.');
    }

    public function destroy(Booking $booking): RedirectResponse
    {
        $booking->delete();

        return redirect()->route('admin.bookings.index')->with('ok', 'Permintaan dihapus.');
    }

    /** Unduh CSV (bisa dibuka di Excel) sesuai filter yang sedang aktif. */
    public function export(Request $request): StreamedResponse
    {
        $rows = $this->filtered($request)->latest();
        $name = 'permintaan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['ID', 'Tanggal', 'Jenis', 'Status', 'Nama', 'Instansi', 'Perangkat', 'Unit', 'Keluhan/Kebutuhan', 'Lokasi', 'Sumber', 'Catatan admin']);

            foreach ($rows->cursor() as $b) {
                fputcsv($out, [
                    $b->id, $b->created_at->format('Y-m-d H:i'), $b->type, $b->status, $b->name, $b->organization,
                    $b->device, $b->units, $b->complaint, $b->location, $b->source, $b->admin_note,
                ]);
            }

            fclose($out);
        }, $name, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function filtered(Request $request): Builder
    {
        return Booking::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->input('q').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)
                    ->orWhere('organization', 'like', $term)
                    ->orWhere('complaint', 'like', $term)
                    ->orWhere('location', 'like', $term));
            });
    }
}
