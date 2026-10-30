<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceTicket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TicketController extends Controller
{
    public const STATES = ['done' => 'Selesai', 'now' => 'Sedang berjalan', 'next' => 'Berikutnya'];

    /** Tahapan umum sebagai titik awal tiket baru. */
    public const DEFAULT_STEPS = ['Diterima', 'Dicek teknisi', 'Dalam perbaikan', 'Siap diambil'];

    public function index(Request $request): View
    {
        $tickets = ServiceTicket::query()
            ->with(['events' => fn ($q) => $q->where('state', 'now')])
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->input('q').'%';
                $q->where(fn ($w) => $w->where('code', 'like', $term)->orWhere('customer_name', 'like', $term)->orWhere('device', 'like', $term));
            })
            ->latest()->paginate(20)->withQueryString();

        return view('admin.tickets.index', ['tickets' => $tickets]);
    }

    public function create(): View
    {
        $events = collect(self::DEFAULT_STEPS)->map(fn ($title, $i) => (object) [
            'title' => $title, 'note' => '', 'occurred_at' => null, 'state' => $i === 0 ? 'now' : 'next',
        ]);

        return view('admin.tickets.form', [
            'ticket' => new ServiceTicket(['code' => $this->nextCode()]),
            'events' => $events,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ticket = DB::transaction(function () use ($request) {
            $ticket = ServiceTicket::create($this->validated($request));
            $this->syncEvents($ticket, $request);

            return $ticket;
        });

        return redirect()->route('admin.tickets.edit', $ticket)->with('ok', 'Tiket dibuat. Pelanggan bisa mengeceknya di halaman Cek Status.');
    }

    public function edit(ServiceTicket $ticket): View
    {
        return view('admin.tickets.form', ['ticket' => $ticket, 'events' => $ticket->events]);
    }

    public function update(Request $request, ServiceTicket $ticket): RedirectResponse
    {
        DB::transaction(function () use ($request, $ticket) {
            $ticket->update($this->validated($request, $ticket));
            $this->syncEvents($ticket, $request);
        });

        return back()->with('ok', 'Tiket diperbarui.');
    }

    public function destroy(ServiceTicket $ticket): RedirectResponse
    {
        $ticket->delete();

        return redirect()->route('admin.tickets.index')->with('ok', 'Tiket dihapus.');
    }

    private function validated(Request $request, ?ServiceTicket $ticket = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        return $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9-]+$/', Rule::unique('service_tickets', 'code')->ignore($ticket?->id)],
            'customer_name' => ['required', 'string', 'max:255'],
            'device' => ['required', 'string', 'max:255'],
            'complaint' => ['nullable', 'string', 'max:255'],
        ], [
            'code.regex' => 'Nomor tiket hanya boleh huruf besar, angka, dan tanda minus.',
        ], [
            'code' => 'nomor tiket', 'customer_name' => 'nama pelanggan', 'device' => 'perangkat', 'complaint' => 'keluhan',
        ]);
    }

    /** Tahapan dikirim sebagai baris berurutan; urutan baris menjadi urutan tampil. */
    private function syncEvents(ServiceTicket $ticket, Request $request): void
    {
        $rows = $request->validate([
            'events' => ['array', 'max:20'],
            'events.*.title' => ['nullable', 'string', 'max:255'],
            'events.*.note' => ['nullable', 'string', 'max:255'],
            'events.*.occurred_at' => ['nullable', 'date'],
            'events.*.state' => ['nullable', 'in:'.implode(',', array_keys(self::STATES))],
        ])['events'] ?? [];

        $ticket->events()->delete();

        $sort = 0;

        foreach ($rows as $row) {
            if (blank($row['title'] ?? null)) {
                continue;
            }

            $ticket->events()->create([
                'title' => $row['title'],
                'note' => $row['note'] ?? null,
                'occurred_at' => $row['occurred_at'] ?? null,
                'state' => $row['state'] ?? 'next',
                'sort' => ++$sort,
            ]);
        }
    }

    private function nextCode(): string
    {
        $last = ServiceTicket::where('code', 'like', 'DSK-%')->pluck('code')
            ->map(fn ($c) => (int) substr($c, 4))->max();

        return 'DSK-'.(($last ?: 1000) + 1);
    }
}
