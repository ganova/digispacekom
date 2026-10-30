<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Konten situs yang bukan data tabel: profil, daftar, segmen beranda, dan landing page iklan. */
class ContentController extends Controller
{
    /** Bagian pengaturan => kunci config('site.*') yang dikelola. */
    public const SECTIONS = [
        'profil' => ['name', 'short', 'tagline', 'phone', 'whatsapp', 'email', 'city', 'address', 'area', 'hours', 'maps_query'],
        'daftar' => ['clients', 'service_areas', 'steps'],
        'segmen' => ['segments'],
        'landing' => ['landings'],
        'layanan' => ['services', 'segments'],
    ];

    public function profile(): View
    {
        return view('admin.settings.profile', ['custom' => Setting::isCustomized(...self::SECTIONS['profil'])]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        // 0813-8502-8980 → 6281385028980 untuk tautan wa.me
        $digits = preg_replace('/\D+/', '', (string) $request->input('whatsapp'));
        $request->merge(['whatsapp' => str_starts_with($digits, '0') ? '62'.substr($digits, 1) : $digits]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'short' => ['required', 'string', 'max:80'],
            'tagline' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'max:30'],
            'whatsapp' => ['required', 'regex:/^62\d{8,13}$/'],
            'email' => ['required', 'email', 'max:120'],
            'city' => ['required', 'string', 'max:60'],
            'address' => ['required', 'string', 'max:255'],
            'area' => ['required', 'string', 'max:80'],
            'hours' => ['required', 'string', 'max:80'],
            'maps_query' => ['required', 'string', 'max:160'],
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Contoh: 0813-8502-8980.',
        ], [
            'name' => 'nama perusahaan', 'short' => 'nama singkat', 'tagline' => 'tagline', 'phone' => 'telepon',
            'email' => 'email', 'city' => 'kota', 'address' => 'alamat', 'area' => 'wilayah layanan',
            'hours' => 'jam operasional', 'maps_query' => 'kata kunci peta',
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, $value);
        }

        return back()->with('ok', 'Profil perusahaan disimpan.');
    }

    public function lists(): View
    {
        return view('admin.settings.lists', ['custom' => Setting::isCustomized(...self::SECTIONS['daftar'])]);
    }

    public function updateLists(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'clients' => ['nullable', 'string', 'max:3000'],
            'service_areas' => ['nullable', 'string', 'max:3000'],
            'steps' => ['required', 'string', 'max:3000'],
        ], [], ['clients' => 'daftar klien', 'service_areas' => 'wilayah layanan', 'steps' => 'langkah layanan']);

        $steps = collect(self::lines($data['steps']))->map(function (string $line) {
            [$title, $text] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

            return [$title, $text];
        })->filter(fn ($s) => $s[0] !== '')->values()->all();

        if (count($steps) === 0) {
            return back()->withInput()->withErrors(['steps' => 'Isi minimal satu langkah.']);
        }

        Setting::put('clients', self::lines($data['clients'] ?? ''));
        Setting::put('service_areas', self::lines($data['service_areas'] ?? ''));
        Setting::put('steps', $steps);

        return back()->with('ok', 'Daftar disimpan.');
    }

    public function segments(): View
    {
        return view('admin.settings.segments', [
            'segments' => config('site.segments'),
            'services' => config('site.services'),
            'custom' => Setting::isCustomized('segments'),
        ]);
    }

    public function updateSegments(Request $request): RedirectResponse
    {
        $slugs = array_keys(config('site.services'));
        $segments = [];

        foreach (config('site.segments') as $key => $segment) {
            $input = $request->input("segments.{$key}", []);
            $label = trim((string) ($input['label'] ?? ''));
            $chosen = array_values(array_intersect($slugs, (array) ($input['services'] ?? [])));

            if ($label === '' || $chosen === []) {
                return back()->withInput()->withErrors(["segments.{$key}" => "Tab \"{$segment['label']}\" butuh nama dan minimal satu layanan."]);
            }

            $segments[$key] = ['label' => mb_substr($label, 0, 40), 'services' => $chosen];
        }

        Setting::put('segments', $segments);

        return back()->with('ok', 'Tab beranda disimpan.');
    }

    public function landings(): View
    {
        return view('admin.landings.index', ['landings' => config('site.landings'), 'custom' => Setting::isCustomized('landings')]);
    }

    public function editLanding(string $slug): View
    {
        $page = config("site.landings.{$slug}") ?? abort(404);

        return view('admin.landings.form', ['slug' => $slug, 'page' => $page]);
    }

    public function updateLanding(Request $request, string $slug): RedirectResponse
    {
        $current = config("site.landings.{$slug}") ?? abort(404);

        $data = $request->validate([
            'meta_title' => ['required', 'string', 'max:120'],
            'meta_description' => ['required', 'string', 'max:300'],
            'headline' => ['required', 'string', 'max:120'],
            'highlight' => ['required', 'string', 'max:120'],
            'lead' => ['required', 'string', 'max:300'],
            'checks' => ['required', 'string', 'max:600'],
            'form_title' => ['required', 'string', 'max:80'],
            'devices' => ['required', 'string', 'max:400'],
            'placeholder' => ['required', 'string', 'max:160'],
        ], [], [
            'meta_title' => 'judul tab', 'meta_description' => 'deskripsi mesin pencari', 'headline' => 'judul utama',
            'highlight' => 'kalimat sorotan', 'lead' => 'kalimat pembuka', 'checks' => 'poin keunggulan',
            'form_title' => 'judul formulir', 'devices' => 'pilihan perangkat', 'placeholder' => 'contoh isian keluhan',
        ]);

        $data['checks'] = self::lines($data['checks']);
        $data['devices'] = self::lines($data['devices']);

        $landings = config('site.landings');
        $landings[$slug] = $data + $current;

        Setting::put('landings', $landings);

        return redirect()->route('admin.landings.index')->with('ok', 'Landing page disimpan.');
    }

    public function reset(string $section): RedirectResponse
    {
        $keys = self::SECTIONS[$section] ?? abort(404);

        Setting::reset(...$keys);

        return back()->with('ok', 'Dikembalikan ke isi awal.');
    }

    /** Satu baris = satu butir; baris kosong dibuang. */
    public static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $text))));
    }
}
