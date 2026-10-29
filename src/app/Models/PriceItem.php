<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PriceItem extends Model
{
    public const CATEGORIES = [
        'laptop' => 'Laptop',
        'pc' => 'PC',
        'macbook' => 'MacBook',
        'printer' => 'Printer',
        'jaringan' => 'Jaringan',
    ];

    protected $fillable = ['category', 'name', 'price_min', 'price_max', 'price_suffix', 'note', 'keywords', 'sort'];

    public function scopeSearch(Builder $query, ?string $term): void
    {
        $words = array_filter(preg_split('/\s+/', mb_strtolower(trim((string) $term))));

        foreach ($words as $word) {
            $query->where(function (Builder $q) use ($word) {
                $q->where('name', 'like', "%{$word}%")
                    ->orWhere('keywords', 'like', "%{$word}%")
                    ->orWhere('category', $word);
            });
        }
    }

    /** Format ringkas: Rp150rb–450rb, Rp350rb + SSD */
    public function priceLabel(): string
    {
        $label = 'Rp'.self::short($this->price_min);

        if ($this->price_max) {
            $label .= '–'.self::short($this->price_max);
        }

        return $this->price_suffix ? "{$label} {$this->price_suffix}" : $label;
    }

    public static function short(int $amount): string
    {
        if ($amount >= 1_000_000) {
            return rtrim(rtrim(number_format($amount / 1_000_000, 1, ',', ''), '0'), ',').'jt';
        }

        return intdiv($amount, 1000).'rb';
    }
}
