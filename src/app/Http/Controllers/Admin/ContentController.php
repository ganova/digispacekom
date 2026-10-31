<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Images;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Konten situs yang bukan data tabel: profil, daftar, segmen beranda, dan landing page iklan. */
class ContentController extends Controller
{
    /** Bagian pengaturan => kunci config('site.*') yang dikelola. */
    public const SECTIONS = [
        'profil' => ['name', 'short', 'tagline', 'phone', 'whatsapp', 'email', 'city', 'address', 'area', 'hours', 'maps_query', 'logo', 'logo_dark', 'logo_show_name', 'favicon', 'favicon_custom'],
        'tentang' => ['about_lead', 'about_body', 'vision', 'mission'],
        'daftar' => ['service_areas', 'steps'],
        'klien' => ['clients'],
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
            'logo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'logo_dark' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:1024'],
        ], [
            'whatsapp.regex' => 'Nomor WhatsApp tidak valid. Contoh: 0813-8502-8980.',
            'logo.mimes' => 'Logo harus berformat PNG, JPG, atau WebP.',
            'logo_dark.mimes' => 'Logo latar gelap harus berformat PNG, JPG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
            'favicon.mimes' => 'Ikon tab harus berformat PNG, JPG, atau WebP.',
            'favicon.max' => 'Ukuran ikon tab maksimal 1 MB.',
            'logo_dark.max' => 'Ukuran logo latar gelap maksimal 2 MB.',
        ], [
            'logo' => 'logo', 'logo_dark' => 'logo latar gelap', 'favicon' => 'ikon tab',
            'name' => 'nama perusahaan', 'short' => 'nama singkat', 'tagline' => 'tagline', 'phone' => 'telepon',
            'email' => 'email', 'city' => 'kota', 'address' => 'alamat', 'area' => 'wilayah layanan',
            'hours' => 'jam operasional', 'maps_query' => 'kata kunci peta',
        ]);

        unset($data['logo'], $data['logo_dark'], $data['favicon']);
        Setting::put('logo_show_name', $request->boolean('logo_show_name'));

        foreach ($data as $key => $value) {
            Setting::put($key, $value);
        }

        if ($failed = $this->saveLogos($request)) {
            return $failed;
        }

        return back()->with('ok', 'Profil perusahaan disimpan.');
    }

    /** Logo baru menggantikan yang lama; kotak "hapus" mengembalikan ke logo bawaan. */
    private function saveLogos(Request $request): ?RedirectResponse
    {
        foreach (['logo' => 'logo_remove', 'logo_dark' => 'logo_dark_remove'] as $key => $removeFlag) {
            $value = $request->boolean($removeFlag) ? null : config("site.{$key}");

            if ($file = $request->file($key)) {
                try {
                    $value = Images::store($file, 'brand', 700);
                } catch (\Throwable $e) {
                    report($e);

                    return back()->withInput()->withErrors([$key => 'Logo tidak bisa disimpan: folder unggahan tidak dapat ditulis. Hubungi admin server.']);
                }
            }

            Setting::put($key, $value);
        }

        $this->saveFavicon($request);

        self::pruneBrand();

        return null;
    }

    /** Ikon tab browser: unggahan sendiri menang; bila tidak ada, diturunkan dari logo yang diunggah. */
    private function saveFavicon(Request $request): void
    {
        $favicon = config('site.favicon');
        $custom = (bool) config('site.favicon_custom');

        if ($request->boolean('favicon_remove')) {
            $favicon = null;
            $custom = false;
        }

        if ($file = $request->file('favicon')) {
            if ($name = Images::squareIcon($file->getRealPath(), 'brand', 192, false)) {
                $favicon = $name;
                $custom = true;
            }
        }

        $logo = config('site.logo');

        if (! $custom && $logo && ($request->hasFile('logo') || $favicon === null)) {
            $favicon = Images::squareIcon(Images::dir('brand').'/'.$logo, 'brand');
        } elseif (! $custom && ! $logo) {
            $favicon = null;
        }

        Setting::put('favicon', $favicon);
        Setting::put('favicon_custom', $custom);
    }

    public static function pruneBrand(): void
    {
        Images::prune('brand', array_values(array_filter([config('site.logo'), config('site.logo_dark'), config('site.favicon')])));
    }

    public function about(): View
    {
        return view('admin.settings.about', ['custom' => Setting::isCustomized(...self::SECTIONS['tentang'])]);
    }

    public function updateAbout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'about_lead' => ['required', 'string', 'max:400'],
            'about_body' => ['nullable', 'string', 'max:3000'],
            'vision' => ['required', 'string', 'max:500'],
            'mission' => ['required', 'string', 'max:1500'],
        ], [], [
            'about_lead' => 'Tentang singkat', 'about_body' => 'Uraian lengkap', 'vision' => 'Visi', 'mission' => 'Misi',
        ]);

        $mission = self::lines($data['mission']);

        if ($mission === []) {
            return back()->withInput()->withErrors(['mission' => 'Isi minimal satu butir misi.']);
        }

        Setting::put('about_lead', trim($data['about_lead']));
        Setting::put('about_body', trim((string) ($data['about_body'] ?? '')));
        Setting::put('vision', trim($data['vision']));
        Setting::put('mission', $mission);

        return back()->with('ok', 'Tentang, visi, dan misi disimpan.');
    }

    public function lists(): View
    {
        return view('admin.settings.lists', ['custom' => Setting::isCustomized(...self::SECTIONS['daftar'])]);
    }

    public function updateLists(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'service_areas' => ['nullable', 'string', 'max:3000'],
            'steps' => ['required', 'string', 'max:3000'],
        ], [], ['service_areas' => 'wilayah layanan', 'steps' => 'langkah layanan']);

        $steps = collect(self::lines($data['steps']))->map(function (string $line) {
            [$title, $text] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');

            return [$title, $text];
        })->filter(fn ($s) => $s[0] !== '')->values()->all();

        if (count($steps) === 0) {
            return back()->withInput()->withErrors(['steps' => 'Isi minimal satu langkah.']);
        }

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

    /** Tab beranda dapat ditambah, dihapus, diberi nama, dan diurutkan; tiap tab memilih layanannya. */
    public function updateSegments(Request $request): RedirectResponse
    {
        $slugs = array_keys(config('site.services'));
        $rows = array_values((array) $request->input('segments', []));

        if (count($rows) < 1 || count($rows) > 8) {
            return back()->withInput()->withErrors(['segments' => 'Beranda butuh 1 sampai 8 tab.']);
        }

        $segments = [];
        $known = config('site.segments');

        foreach ($rows as $row) {
            $label = mb_substr(trim((string) ($row['label'] ?? '')), 0, 40);
            $chosen = array_values(array_intersect($slugs, (array) ($row['services'] ?? [])));

            if ($label === '' || $chosen === []) {
                return back()->withInput()->withErrors(['segments' => 'Setiap tab butuh nama dan minimal satu layanan.']);
            }

            // kunci tab dipertahankan bila sudah ada; tab baru memakai nama sebagai kunci
            $key = isset($row['key'], $known[$row['key']]) ? $row['key'] : (Str::slug($label) ?: 'tab');
            $base = $key;
            $i = 2;

            while (isset($segments[$key])) {
                $key = $base.'-'.$i++;
            }

            $segments[$key] = ['label' => $label, 'services' => $chosen];
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

        if ($section === 'klien') {
            ClientController::prune();
        }

        if ($section === 'profil') {
            self::pruneBrand();
        }

        return back()->with('ok', 'Dikembalikan ke isi awal.');
    }

    /** Satu baris = satu butir; baris kosong dibuang. */
    public static function lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/u', (string) $text))));
    }
}
