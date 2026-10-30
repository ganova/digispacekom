<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PriceController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\AdminHeaders;
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

        // Permintaan masuk dari form booking & penawaran
        Route::get('permintaan/unduh', [BookingController::class, 'export'])->name('bookings.export');
        Route::resource('permintaan', BookingController::class)->parameters(['permintaan' => 'booking'])
            ->only(['index', 'show', 'update', 'destroy'])->names('bookings');

        Route::resource('harga', PriceController::class)->parameters(['harga' => 'price'])->except('show')->names('prices');
        Route::resource('artikel', ArticleController::class)->parameters(['artikel' => 'article'])->except('show')->names('articles');
        Route::resource('tiket', TicketController::class)->parameters(['tiket' => 'ticket'])->except('show')->names('tickets');

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

        // Profil perusahaan & daftar
        Route::get('profil', [ContentController::class, 'profile'])->name('profile');
        Route::put('profil', [ContentController::class, 'updateProfile'])->name('profile.update');
        Route::get('daftar', [ContentController::class, 'lists'])->name('lists');
        Route::put('daftar', [ContentController::class, 'updateLists'])->name('lists.update');
        Route::post('kembalikan/{section}', [ContentController::class, 'reset'])->name('reset');

        // Akun
        Route::get('akun', [UserController::class, 'account'])->name('account');
        Route::put('akun', [UserController::class, 'updateAccount'])->name('account.update');
        Route::get('admin-baru', [UserController::class, 'create'])->name('users.create');
        Route::get('pengguna', [UserController::class, 'index'])->name('users.index');
        Route::post('pengguna', [UserController::class, 'store'])->name('users.store');
        Route::delete('pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});
