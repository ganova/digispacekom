<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PriceController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// Company profile (Konsep B)
Route::get('/', [SiteController::class, 'home'])->name('home');
Route::get('/layanan', [SiteController::class, 'services'])->name('services.index');
Route::get('/layanan/{slug}', [SiteController::class, 'service'])->name('services.show');
Route::get('/tentang', [SiteController::class, 'about'])->name('about');
Route::get('/kontak', [SiteController::class, 'contact'])->name('contact');
Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article}', [ArticleController::class, 'show'])->name('articles.show');

// Cek harga & status servis (Konsep C)
Route::get('/cek-harga', [PriceController::class, 'index'])->name('prices.index');
Route::get('/cek-status', [PriceController::class, 'status'])->name('status');

Route::post('/booking', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// CMS admin
require __DIR__.'/admin.php';

// Landing page iklan (Konsep A)
Route::get('/{landing}', [SiteController::class, 'landing'])
    ->whereIn('landing', array_keys(config('site.landings')))
    ->name('landing');
