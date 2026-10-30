<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Booking;
use App\Models\PriceItem;
use App\Models\ServiceTicket;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'newBookings' => Booking::where('status', 'baru')->count(),
            'weekBookings' => Booking::where('created_at', '>=', now()->subDays(7))->count(),
            'tickets' => ServiceTicket::count(),
            'prices' => PriceItem::count(),
            'articles' => Article::count(),
            'drafts' => Article::whereNull('published_at')->count(),
            'latest' => Booking::latest()->limit(6)->get(),
        ]);
    }
}
