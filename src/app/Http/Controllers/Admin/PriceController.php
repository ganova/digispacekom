<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceItem;
use App\Support\Images;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PriceController extends Controller
{
    public function index(Request $request): View
    {
        $items = PriceItem::query()
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->input('category')))
            ->when($request->filled('q'), fn ($q) => $q->search($request->input('q')))
            ->orderBy('category')->orderBy('sort')->orderBy('id')
            ->paginate(30)->withQueryString();

        return view('admin.prices.index', ['items' => $items]);
    }

    public function create(): View
    {
        return view('admin.prices.form', ['item' => new PriceItem(['sort' => 0])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        if ($failed = $this->saveImage($request, $data)) {
            return $failed;
        }

        PriceItem::create($data);

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga ditambahkan.');
    }

    public function edit(PriceItem $price): View
    {
        return view('admin.prices.form', ['item' => $price]);
    }

    public function update(Request $request, PriceItem $price): RedirectResponse
    {
        $data = $this->validated($request);
        $old = $price->image;

        if ($failed = $this->saveImage($request, $data, $old)) {
            return $failed;
        }

        $price->update($data);

        if ($old && $old !== $price->image) {
            Images::delete('prices', $old);
        }

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga diperbarui.');
    }

    public function destroy(PriceItem $price): RedirectResponse
    {
        Images::delete('prices', $price->image);
        $price->delete();

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga dihapus.');
    }

    /** Foto baru menggantikan yang lama; kotak "hapus foto" mengosongkannya. */
    private function saveImage(Request $request, array &$data, ?string $current = null): ?RedirectResponse
    {
        unset($data['photo'], $data['photo_remove']);
        $data['image'] = $request->boolean('photo_remove') ? null : $current;

        if ($file = $request->file('photo')) {
            try {
                $data['image'] = Images::store($file, 'prices', 900);
            } catch (\Throwable $e) {
                report($e);

                return back()->withInput()->withErrors(['photo' => 'Foto tidak bisa disimpan: folder unggahan tidak dapat ditulis. Hubungi admin server.']);
            }
        }

        return null;
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_keys(PriceItem::categories()))],
            'name' => ['required', 'string', 'max:255'],
            'price_min' => ['required', 'integer', 'min:0', 'max:999999999'],
            'price_max' => ['nullable', 'integer', 'gte:price_min', 'max:999999999'],
            'price_suffix' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:120'],
            'stock' => ['nullable', 'in:'.implode(',', array_keys(PriceItem::STOCK))],
            'photo' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:6144'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ], [
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WebP.',
            'photo.max' => 'Ukuran foto maksimal 6 MB.',
        ], [
            'category' => 'Kategori', 'name' => 'Nama layanan', 'price_min' => 'Harga mulai', 'price_max' => 'Harga sampai',
            'price_suffix' => 'Keterangan harga', 'note' => 'Catatan', 'keywords' => 'Kata kunci', 'sort' => 'Urutan',
            'stock' => 'Stok', 'photo' => 'Foto',
        ]);

        $data['sort'] = $data['sort'] ?? 0;
        $data['stock'] = ($data['stock'] ?? null) ?: null;

        return $data;
    }
}
