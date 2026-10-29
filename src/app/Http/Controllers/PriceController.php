<?php

namespace App\Http\Controllers;

use App\Models\PriceItem;
use App\Models\ServiceTicket;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PriceController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q'));
        $category = array_key_exists((string) $request->query('kategori'), PriceItem::CATEGORIES)
            ? $request->query('kategori')
            : null;

        $items = PriceItem::query()
            ->search($q)
            ->when($category, fn ($query) => $query->where('category', $category))
            ->orderBy('sort')
            ->get();

        // Permintaan dari pencarian langsung (JS) cukup menerima daftar hasilnya saja
        if ($request->hasHeader('X-Partial')) {
            return view('prices._list', compact('items', 'q'));
        }

        return view('prices.index', compact('items', 'q', 'category'));
    }

    public function status(Request $request): View
    {
        $code = strtoupper(trim((string) $request->query('tiket')));
        $ticket = $code !== '' ? ServiceTicket::with('events')->where('code', $code)->first() : null;

        return view('prices.status', compact('code', 'ticket'));
    }
}
