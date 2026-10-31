<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceItem;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Kategori di halaman Cek Harga (mis. Laptop, Printer, Sparepart, CCTV). */
class PriceCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.prices.categories', [
            'categories' => PriceItem::categories(),
            'counts' => PriceItem::selectRaw('category, count(*) as n')->groupBy('category')->pluck('n', 'category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['label' => ['required', 'string', 'max:30']], [], ['label' => 'Nama kategori']);
        $categories = PriceItem::categories();

        $base = Str::slug($data['label']) ?: 'kategori';
        $key = $base;
        $i = 2;

        while (isset($categories[$key])) {
            $key = $base.'-'.$i++;
        }

        $categories[$key] = trim($data['label']);
        Setting::put('price_categories', $categories);

        return back()->with('ok', "Kategori “{$data['label']}” ditambahkan.");
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $categories = PriceItem::categories();
        abort_unless(isset($categories[$key]), 404);

        $data = $request->validate(['label' => ['required', 'string', 'max:30']], [], ['label' => 'Nama kategori']);

        $categories[$key] = trim($data['label']);
        Setting::put('price_categories', $categories);

        return back()->with('ok', 'Nama kategori diperbarui.');
    }

    public function move(Request $request, string $key): RedirectResponse
    {
        $categories = PriceItem::categories();
        $keys = array_keys($categories);
        $pos = array_search($key, $keys, true);
        abort_if($pos === false, 404);

        $to = $request->input('dir') === 'up' ? $pos - 1 : $pos + 1;

        if (isset($keys[$to])) {
            [$keys[$pos], $keys[$to]] = [$keys[$to], $keys[$pos]];
            Setting::put('price_categories', collect($keys)->mapWithKeys(fn ($k) => [$k => $categories[$k]])->all());
        }

        return back();
    }

    public function destroy(string $key): RedirectResponse
    {
        $categories = PriceItem::categories();
        abort_unless(isset($categories[$key]), 404);

        if (count($categories) <= 1) {
            return back()->withErrors(['categories' => 'Minimal harus ada satu kategori.']);
        }

        $used = PriceItem::where('category', $key)->count();

        if ($used > 0) {
            return back()->withErrors(['categories' => "Kategori “{$categories[$key]}” masih dipakai {$used} item harga. Pindahkan atau hapus item itu dulu."]);
        }

        // layanan yang menautkan kategori ini dilepas agar tombol "Lihat estimasi harga" tidak rusak
        $services = collect(config('site.services'))->map(function (array $service) use ($key) {
            if (($service['price_category'] ?? null) === $key) {
                $service['price_category'] = null;
            }

            return $service;
        })->all();

        unset($categories[$key]);
        Setting::put('price_categories', $categories);
        Setting::put('services', $services);

        return back()->with('ok', 'Kategori dihapus.');
    }
}
