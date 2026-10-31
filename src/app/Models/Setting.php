<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Konten situs yang diedit lewat CMS. Setiap baris menimpa satu kunci config('site.*');
 * bila barisnya tidak ada, nilai bawaan dari config/site.php dipakai.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return ['value' => 'json'];
    }

    /** Semua nilai tersimpan; kosong bila tabel belum ada (mis. sebelum migrasi). */
    public static function overrides(): array
    {
        try {
            return static::query()->get()->mapWithKeys(fn (self $s) => [$s->key => $s->value])->all();
        } catch (\Throwable) {
            return [];
        }
    }

    public static function put(string $key, mixed $value): void
    {
        // kolom value tidak menerima NULL; nilai kosong cukup menghapus barisnya
        if ($value === null) {
            static::where('key', $key)->delete();
        } else {
            static::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        config(["site.{$key}" => $value]);
    }

    /** Hapus perubahan dan kembali ke nilai bawaan di config/site.php. */
    public static function reset(string ...$keys): void
    {
        static::whereIn('key', $keys)->delete();

        $defaults = require config_path('site.php');

        foreach ($keys as $key) {
            config(["site.{$key}" => $defaults[$key] ?? null]);
        }
    }

    public static function isCustomized(string ...$keys): bool
    {
        return static::whereIn('key', $keys)->exists();
    }
}
