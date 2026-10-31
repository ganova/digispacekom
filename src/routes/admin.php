<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Admin\PriceCategoryController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminHeaders;
use App\Http\Middleware\RequireAdmin;
use Illuminate\Support\Facades\Route;

// CMS: seluruh konten situs dikelola dari sini
Route::prefix('admin')->name('admin.')->middleware(AdminHeaders::class)->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('masuk', [AuthController::class, 'show'])->name('login');
        Route::post('masuk', [AuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    });

    Route::middleware('auth')->group(function () {
        Route::post('keluar', [AuthController::class, 'logout'])->name('logout');
        Route::get('/', DashboardController::class)->name('dashboard');

        // Akun sendiri (admin maupun staf)
        Route::get('akun', [UserController::class, 'account'])->name('account');
        Route::put('akun', [UserController::class, 'updateAccount'])->name('account.update');

        // Dapat dikerjakan staf: daftar harga & suku cadang, artikel, tiket servis
        Route::post('unggah-gambar', ImageUploadController::class)->middleware('throttle:60,1')->name('upload');

        // Kategori harga (mis. Sparepart, CCTV) ikut dikelola staf bersama daftar harga
        Route::get('kategori-harga', [PriceCategoryController::class, 'index'])->name('price-categories');
        Route::post('kategori-harga', [PriceCategoryController::class, 'store'])->name('price-categories.store');
        Route::put('kategori-harga/{key}', [PriceCategoryController::class, 'update'])->name('price-categories.update');
        Route::post('kategori-harga/{key}/pindah', [PriceCategoryController::class, 'move'])->name('price-categories.move');
        Route::delete('kategori-harga/{key}', [PriceCategoryController::class, 'destroy'])->name('price-categories.destroy');

        Route::resource('harga', PriceController::class)->parameters(['harga' => 'price'])->except('show')->names('prices');
        Route::resource('artikel', ArticleController::class)->parameters(['artikel' => 'article'])->except('show')->names('articles');
        Route::resource('tiket', TicketController::class)->parameters(['tiket' => 'ticket'])->except('show')->names('tickets');

        // Hanya admin penuh
        Route::middleware(RequireAdmin::class)->group(function () {
            // Permintaan masuk dari form booking & penawaran
            Route::get('permintaan/unduh', [BookingController::class, 'export'])->name('bookings.export');
            Route::resource('permintaan', BookingController::class)->parameters(['permintaan' => 'booking'])
                ->only(['index', 'show', 'update', 'destroy'])->names('bookings');

            // Layanan, tab beranda, landing page
            Route::get('layanan', [ServiceController::class, 'index'])->name('services.index');
            Route::get('layanan/baru', [ServiceController::class, 'create'])->name('services.create');
            Route::post('layanan', [ServiceController::class, 'store'])->name('services.store');
            Route::get('layanan/{slug}', [ServiceController::class, 'edit'])->name('services.edit');
            Route::put('layanan/{slug}', [ServiceController::class, 'update'])->name('services.update');
            Route::delete('layanan/{slug}', [ServiceController::class, 'destroy'])->name('services.destroy');
            Route::post('layanan/{slug}/pindah', [ServiceController::class, 'move'])->name('services.move');

            Route::get('tab-beranda', [ContentController::class, 'segments'])->name('segments');
            Route::put('tab-beranda', [ContentController::class, 'updateSegments'])->name('segments.update');

            Route::get('landing', [ContentController::class, 'landings'])->name('landings.index');
            Route::get('landing/{slug}', [ContentController::class, 'editLanding'])->name('landings.edit');
            Route::put('landing/{slug}', [ContentController::class, 'updateLanding'])->name('landings.update');

            // Klien & logo
            Route::get('klien', [ClientController::class, 'index'])->name('clients');
            Route::put('klien', [ClientController::class, 'update'])->name('clients.update');

            // Profil perusahaan & daftar
            Route::get('profil', [ContentController::class, 'profile'])->name('profile');
            Route::put('profil', [ContentController::class, 'updateProfile'])->name('profile.update');
            Route::get('daftar', [ContentController::class, 'lists'])->name('lists');
            Route::put('daftar', [ContentController::class, 'updateLists'])->name('lists.update');
            Route::post('kembalikan/{section}', [ContentController::class, 'reset'])->name('reset');

            // Pengguna CMS
            Route::get('pengguna', [UserController::class, 'index'])->name('users.index');
            Route::get('pengguna/baru', [UserController::class, 'create'])->name('users.create');
            Route::post('pengguna', [UserController::class, 'store'])->name('users.store');
            Route::get('pengguna/{user}/ubah', [UserController::class, 'edit'])->name('users.edit');
            Route::put('pengguna/{user}', [UserController::class, 'update'])->name('users.update');
            Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        });
    });
});
