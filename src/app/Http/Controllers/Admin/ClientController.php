<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Images;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Klien yang dipercaya: nama dan logo (opsional) yang tampil di beranda, landing, dan halaman Tentang. */
class ClientController extends Controller
{
    public function index(): View
    {
        return view('admin.clients.index', [
            'clients' => old('clients') ? $this->fromOld(old('clients')) : Site::clients(),
            'custom' => Setting::isCustomized('clients'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'clients' => ['nullable', 'array', 'max:60'],
            'clients.*.name' => ['nullable', 'string', 'max:80'],
            'clients.*.logo_current' => ['nullable', 'string', 'regex:'.Images::NAME],
            'clients.*.logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ], [
            'clients.*.logo.mimes' => 'Logo harus berformat PNG, JPG, atau WebP.',
            'clients.*.logo.max' => 'Ukuran logo maksimal 2 MB.',
            'clients.*.logo.uploaded' => 'Logo gagal diunggah. Pastikan ukurannya tidak lebih dari 2 MB.',
        ]);

        $existing = collect(Site::clients())->pluck('logo')->filter()->all();
        $clients = [];

        foreach ($request->input('clients', []) as $i => $row) {
            $name = trim((string) ($row['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            // hanya logo yang memang sudah dimiliki daftar ini yang boleh dipertahankan
            $logo = in_array($row['logo_current'] ?? null, $existing, true) ? $row['logo_current'] : null;

            if (! empty($row['logo_remove'])) {
                $logo = null;
            }

            if ($file = $request->file("clients.{$i}.logo")) {
                try {
                    $logo = Images::store($file, 'clients', 600);
                } catch (\Throwable $e) {
                    report($e);

                    return back()->withInput()->withErrors([
                        'clients' => 'Logo tidak bisa disimpan: folder unggahan tidak dapat ditulis. Hubungi admin server (jalankan ./prod.sh up -d --build).',
                    ]);
                }
            }

            $clients[] = ['name' => $name, 'logo' => $logo];
        }

        Setting::put('clients', $clients);
        self::prune();

        return back()->with('ok', 'Daftar klien disimpan.');
    }

    /** Hapus berkas logo yang tidak lagi dipakai daftar klien. */
    public static function prune(): void
    {
        Images::prune('clients', collect(Site::clients())->pluck('logo')->filter()->all());
    }

    /** Bentuk ulang isian formulir yang gagal divalidasi agar nama dan logo lama tidak hilang. */
    private function fromOld(array $rows): array
    {
        return collect($rows)->map(function ($row) {
            $logo = $row['logo_current'] ?? null;

            return [
                'name' => (string) ($row['name'] ?? ''),
                'logo' => $logo,
                'logo_url' => Images::url('clients', $logo),
            ];
        })->values()->all();
    }
}
