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

    /** URL aset publik dengan versi berdasarkan waktu ubah file (cache busting). */
    public static function asset(string $path): string
    {
        $file = public_path($path);

        return asset($path).(is_file($file) ? '?v='.filemtime($file) : '');
    }

    public static function whatsappUrl(?string $text = null): string
    {
        $url = 'https://wa.me/'.config('site.whatsapp');

        return $text ? $url.'?text='.rawurlencode($text) : $url;
    }
}
