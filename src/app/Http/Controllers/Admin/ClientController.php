<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Site;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Klien yang dipercaya: nama dan logo (opsional) yang tampil di beranda, landing, dan halaman Tentang. */
class ClientController extends Controller
{
    /** Folder logo di dalam public/. Dapat dialihkan lewat config('site.upload_dir') (dipakai oleh tes). */
    public static function dir(): string
    {
        return config('site.upload_dir') ?: public_path('uploads/clients');
    }

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
            'clients.*.logo_current' => ['nullable', 'string', 'regex:/^[A-Za-z0-9_-]+\.(png|jpe?g|webp)$/'],
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
                $logo = Str::random(20).'.'.$file->extension();

                try {
                    File::ensureDirectoryExists(self::dir());
                    $file->move(self::dir(), $logo);
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
        $this->prune();

        return back()->with('ok', 'Daftar klien disimpan.');
    }

    /** Hapus berkas logo yang tidak lagi dipakai daftar klien. */
    public static function prune(): void
    {
        $dir = self::dir();

        if (! File::isDirectory($dir)) {
            return;
        }

        $used = collect(Site::clients())->pluck('logo')->filter()->all();

        foreach (File::files($dir) as $file) {
            if (! in_array($file->getFilename(), $used, true)) {
                File::delete($file->getPathname());
            }
        }
    }

    /** Bentuk ulang isian formulir yang gagal divalidasi agar nama dan logo lama tidak hilang. */
    private function fromOld(array $rows): array
    {
        return collect($rows)->map(function ($row) {
            $logo = $row['logo_current'] ?? null;

            return [
                'name' => (string) ($row['name'] ?? ''),
                'logo' => $logo,
                'logo_url' => $logo ? asset('uploads/clients/'.$logo) : null,
            ];
        })->values()->all();
    }
}
