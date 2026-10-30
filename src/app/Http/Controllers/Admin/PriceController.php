<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PriceItem;
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
        PriceItem::create($this->validated($request));

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga ditambahkan.');
    }

    public function edit(PriceItem $price): View
    {
        return view('admin.prices.form', ['item' => $price]);
    }

    public function update(Request $request, PriceItem $price): RedirectResponse
    {
        $price->update($this->validated($request));

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga diperbarui.');
    }

    public function destroy(PriceItem $price): RedirectResponse
    {
        $price->delete();

        return redirect()->route('admin.prices.index')->with('ok', 'Item harga dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category' => ['required', 'in:'.implode(',', array_keys(PriceItem::CATEGORIES))],
            'name' => ['required', 'string', 'max:255'],
            'price_min' => ['required', 'integer', 'min:0', 'max:999999999'],
            'price_max' => ['nullable', 'integer', 'gte:price_min', 'max:999999999'],
            'price_suffix' => ['nullable', 'string', 'max:40'],
            'note' => ['nullable', 'string', 'max:120'],
            'keywords' => ['nullable', 'string', 'max:255'],
            'sort' => ['nullable', 'integer', 'min:0', 'max:65000'],
        ], [], [
            'name' => 'nama layanan', 'price_min' => 'harga mulai', 'price_max' => 'harga sampai',
            'price_suffix' => 'keterangan harga', 'note' => 'catatan', 'keywords' => 'kata kunci', 'sort' => 'urutan',
        ]);

        $data['sort'] = $data['sort'] ?? 0;

        return $data;
    }
}
