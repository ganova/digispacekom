<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceItem;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Daftar layanan situs (halaman /layanan dan detailnya). */
class ServiceController extends Controller
{
    public const ICONS = [
        'laptop' => 'Laptop', 'monitor' => 'Monitor / MacBook', 'printer' => 'Printer', 'server' => 'Server',
        'chip' => 'Chip / PC', 'box' => 'Kotak / Pengadaan', 'shield' => 'Perisai', 'pin' => 'Lokasi',
    ];

    public function index(): View
    {
        return view('admin.services.index', [
            'services' => config('site.services'),
            'custom' => Setting::isCustomized('services'),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', ['slug' => null, 'service' => ['icon' => 'laptop', 'price_category' => null, 'problems' => []]]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $services = config('site.services');

        $base = Str::slug($data['title']) ?: 'layanan';
        $slug = $base;
        $i = 2;

        while (isset($services[$slug]) || $slug === 'tidak-ada') {
            $slug = $base.'-'.$i++;
        }

        $services[$slug] = $data;
        Setting::put('services', $services);

        return redirect()->route('admin.services.index')->with('ok', 'Layanan ditambahkan. Atur tampilannya di tab beranda lewat menu Tab Beranda.');
    }

    public function edit(string $slug): View
    {
        $service = config("site.services.{$slug}") ?? abort(404);

        return view('admin.services.form', ['slug' => $slug, 'service' => $service]);
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        config("site.services.{$slug}") ?? abort(404);

        $services = config('site.services');
        $services[$slug] = $this->validated($request);
        Setting::put('services', $services);

        return redirect()->route('admin.services.index')->with('ok', 'Layanan disimpan.');
    }

    public function destroy(string $slug): RedirectResponse
    {
        $services = config('site.services');

        if (! isset($services[$slug])) {
            abort(404);
        }

        if (count($services) <= 1) {
            return back()->withErrors(['services' => 'Minimal harus ada satu layanan.']);
        }

        unset($services[$slug]);

        // layanan yang dihapus juga dilepas dari tab beranda; tab yang kosong diisi seluruh layanan
        $segments = collect(config('site.segments'))->map(function (array $segment) use ($slug, $services) {
            $segment['services'] = array_values(array_diff($segment['services'], [$slug]));

            if ($segment['services'] === []) {
                $segment['services'] = array_keys($services);
            }

            return $segment;
        })->all();

        Setting::put('services', $services);
        Setting::put('segments', $segments);

        return redirect()->route('admin.services.index')->with('ok', 'Layanan dihapus.');
    }

    public function move(Request $request, string $slug): RedirectResponse
    {
        $services = config('site.services');
        $keys = array_keys($services);
        $pos = array_search($slug, $keys, true);

        if ($pos === false) {
            abort(404);
        }

        $to = $request->input('dir') === 'up' ? $pos - 1 : $pos + 1;

        if (isset($keys[$to])) {
            [$keys[$pos], $keys[$to]] = [$keys[$to], $keys[$pos]];
            Setting::put('services', collect($keys)->mapWithKeys(fn ($k) => [$k => $services[$k]])->all());
        }

        return back();
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'icon' => ['required', 'in:'.implode(',', array_keys(self::ICONS))],
            'short' => ['required', 'string', 'max:140'],
            'price_category' => ['nullable', 'in:'.implode(',', array_keys(PriceItem::CATEGORIES))],
            'intro' => ['required', 'string', 'max:700'],
            'problems' => ['required', 'string', 'max:1500'],
        ], [], [
            'title' => 'nama layanan', 'icon' => 'ikon', 'short' => 'ringkasan', 'price_category' => 'kategori harga',
            'intro' => 'pengantar', 'problems' => 'daftar masalah',
        ]);

        $data['problems'] = ContentController::lines($data['problems']);
        $data['price_category'] = $data['price_category'] ?: null;

        return $data;
    }
}
