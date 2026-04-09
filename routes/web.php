<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MakananController; // <-- Tambahkan ini
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup untuk user yang sudah login (Bisa diakses Admin & Pemilik)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute untuk Makanan (CRUD Jajanan)
    Route::resource('makanan', MakananController::class);

    // Rute buatanmu: Hanya bisa diakses oleh Pemilik
    Route::get('/log-aktivitas', [LogController::class, 'index'])
        ->middleware('role:Pemilik')
        ->name('log.aktivitas');

    // Rute Transaksi (Barang Masuk/Keluar) - Bisa diakses Admin & Pemilik
    Route::get('/Keluar-Masuk Barang', [LogController::class, 'create'])->name('log.create');
    Route::post('/Keluar-Masuk Barang', [LogController::class, 'store'])->name('log.store');

    // Rute Laporan: Hanya bisa diakses oleh Pemilik
    Route::get('/log-aktivitas', [LogController::class, 'index'])
        ->middleware('role:Pemilik')
        ->name('log.aktivitas');
});

require __DIR__.'/auth.php';