<?php

namespace App\Support;

class Site
{
    public static function services(): array
    {
        return config('site.services');
    }

    public static function service(string $slug): ?array
    {
        $service = config("site.services.{$slug}");

        return $service ? ['slug' => $slug] + $service : null;
    }

    /**
     * Klien yang dipercaya. Bentuk bawaan di config berupa daftar nama; dari CMS berupa
     * daftar [name, logo]. Selalu dikembalikan sebagai daftar [name, logo, logo_url].
     */
    public static function clients(): array
    {
        return collect(config('site.clients'))->map(function ($client) {
            $client = is_array($client) ? $client : ['name' => (string) $client];
            $logo = $client['logo'] ?? null;

            return [
                'name' => $client['name'],
                'logo' => $logo,
                'logo_url' => $logo ? asset('uploads/clients/'.$logo) : null,
            ];
        })->all();
    }

    /** URL aset publik dengan versi berdasarkan waktu ubah file (cache busting). */
    public static function asset(string $path): string
    {
        $file = public_path($path);

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }

    /**
     * Pecah judul menjadi kata untuk animasi "naik per kata".
     * $parts: [teks, kelas-opsional] — contoh [['Mitra IT untuk'], ['kantor.', 'grad']]
     */
    public static function split(array $parts): string
    {
        $html = '';
        $i = 0;

        foreach ($parts as $part) {
            [$text, $class] = $part + [1 => null];

            foreach (preg_split('/\s+/', trim($text)) as $word) {
                $cls = $class ? ' class="'.e($class).'"' : '';
                $html .= '<span class="w"><span'.$cls.' style="--wi:'.$i++.'">'.e($word).'</span></span> ';
            }
        }

        return rtrim($html);
    }

    public static function whatsappUrl(?string $text = null): string
    {
        $url = 'https://wa.me/'.config('site.whatsapp');

        return $text ? $url.'?text='.rawurlencode($text) : $url;
    }
}
