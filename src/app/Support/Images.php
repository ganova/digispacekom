<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Gambar yang diunggah dari CMS (logo klien, sampul artikel, foto suku cadang).
 * Disimpan di public/uploads/{folder} dengan nama acak; foto besar dikecilkan dan diubah ke WebP.
 */
class Images
{
    /** Pola nama berkas yang sah; dipakai untuk menolak nama berkas titipan dari formulir. */
    public const NAME = '/^[A-Za-z0-9_-]+\.(png|jpe?g|webp)$/';

    public static function root(): string
    {
        return rtrim(config('site.upload_root') ?: public_path('uploads'), '/\\');
    }

    public static function dir(string $folder): string
    {
        return self::root().'/'.trim($folder, '/');
    }

    public static function url(string $folder, ?string $file): ?string
    {
        return $file ? asset('uploads/'.trim($folder, '/').'/'.$file) : null;
    }

    /**
     * Simpan gambar dan kembalikan nama berkasnya.
     * Foto lebih lebar dari $maxWidth dikecilkan; hasilnya WebP bila GD tersedia, bila tidak berkas asli disalin apa adanya.
     *
     * @throws \Throwable bila folder tidak bisa ditulis
     */
    public static function store(UploadedFile $file, string $folder, int $maxWidth = 1600, int $quality = 82): string
    {
        $dir = self::dir($folder);
        File::ensureDirectoryExists($dir);

        $base = Str::random(20);

        if ($name = self::optimise($file, $dir, $base, $maxWidth, $quality)) {
            return $name;
        }

        $name = $base.'.'.$file->extension();
        $file->move($dir, $name);

        return $name;
    }

    /**
     * Ikon persegi PNG (untuk tab browser) dari sebuah gambar. Logo yang jauh lebih lebar daripada tingginya
     * biasanya berisi ikon di kiri dan tulisan di kanan, jadi yang diambil kotak di sisi kiri.
     */
    public static function squareIcon(string $sourcePath, string $folder, int $size = 192, bool $cropWide = true): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagepng') || ! is_file($sourcePath)) {
            return null;
        }

        try {
            $src = @imagecreatefromstring(file_get_contents($sourcePath));

            if (! $src) {
                return null;
            }

            $w = imagesx($src);
            $h = imagesy($src);

            if ($cropWide && $w > $h * 1.4 && ($cropped = imagecrop($src, ['x' => 0, 'y' => 0, 'width' => $h, 'height' => $h]))) {
                $src = $cropped;
                $w = $h;
            }

            $scale = min($size / $w, $size / $h);
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));

            $dst = imagecreatetruecolor($size, $size);
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            imagefill($dst, 0, 0, imagecolorallocatealpha($dst, 0, 0, 0, 127));
            imagealphablending($dst, true);
            imagecopyresampled($dst, $src, intdiv($size - $nw, 2), intdiv($size - $nh, 2), 0, 0, $nw, $nh, $w, $h);
            imagesavealpha($dst, true);

            File::ensureDirectoryExists(self::dir($folder));
            $name = 'fav-'.Str::random(16).'.png';

            return imagepng($dst, self::dir($folder).'/'.$name) ? $name : null;
        } catch (\Throwable) {
            return null;
        }
    }

    public static function delete(string $folder, ?string $file): void
    {
        if ($file && preg_match(self::NAME, $file)) {
            File::delete(self::dir($folder).'/'.$file);
        }
    }

    /** Hapus semua berkas di folder yang tidak ada dalam daftar $keep. */
    public static function prune(string $folder, array $keep): void
    {
        $dir = self::dir($folder);

        if (! File::isDirectory($dir)) {
            return;
        }

        foreach (File::files($dir) as $file) {
            if (! in_array($file->getFilename(), $keep, true)) {
                File::delete($file->getPathname());
            }
        }
    }

    private static function optimise(UploadedFile $file, string $dir, string $base, int $maxWidth, int $quality): ?string
    {
        if (! function_exists('imagecreatefromstring') || ! function_exists('imagewebp')) {
            return null;
        }

        try {
            $bytes = file_get_contents($file->getRealPath());
            $img = @imagecreatefromstring($bytes);

            if (! $img) {
                return null;
            }

            // foto dari HP menyimpan arah putar di EXIF; terapkan sebelum EXIF dibuang
            if (function_exists('exif_read_data') && in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
                $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
                $img = match ($orientation) {
                    3 => imagerotate($img, 180, 0),
                    6 => imagerotate($img, -90, 0),
                    8 => imagerotate($img, 90, 0),
                    default => $img,
                } ?: $img;
            }

            if (! imageistruecolor($img)) {
                imagepalettetotruecolor($img);
            }

            if (imagesx($img) > $maxWidth) {
                $img = imagescale($img, $maxWidth, -1, IMG_BICUBIC) ?: $img;
            }

            imagealphablending($img, false);
            imagesavealpha($img, true);

            $name = $base.'.webp';

            if (! imagewebp($img, $dir.'/'.$name, $quality)) {
                return null;
            }

            return $name;
        } catch (\Throwable) {
            return null;
        }
    }
}
