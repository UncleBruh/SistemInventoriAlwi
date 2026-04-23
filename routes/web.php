<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MakananController;
use App\Http\Controllers\MutasiMasukController; // Tambahkan ini
use App\Http\Controllers\MutasiKeluarController; // Tambahkan ini
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup untuk user yang sudah login (Admin & Pemilik)
Route::middleware('auth')->group(function () {
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('makanan', MakananController::class);
    Route::get('/api/makanan/find-by-barcode/{barcode}', [MakananController::class, 'findByBarcode']);

    // --- BAGIAN MUTASI BARANG ---

    // 1. Barang Masuk (Admin & Pemilik bisa akses)
    Route::get('/barang-masuk', [MutasiMasukController::class, 'create'])->name('log.create');
    Route::post('/barang-masuk', [MutasiMasukController::class, 'store'])->name('log.store');

    // 2. Fitur Khusus Pemilik (Barang Keluar & Laporan)
    Route::middleware('role:Pemilik')->group(function () {
        
        // Halaman Riwayat/Laporan Mutasi
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log.aktivitas');
        
        // Halaman Barang Keluar
        Route::get('/barang-keluar', [MutasiKeluarController::class, 'create'])->name('log.keluar.create');
        Route::post('/barang-keluar', [MutasiKeluarController::class, 'store'])->name('log.keluar.store');
    });
});

require __DIR__.'/auth.php';