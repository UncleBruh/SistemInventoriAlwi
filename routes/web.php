<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakananController;
use App\Http\Controllers\MutasiMasukController;
use App\Http\Controllers\MutasiKeluarController;
use Illuminate\Support\Facades\Route;

// Redirect halaman utama ke login
Route::redirect('/', '/login');

// Rute Dashboard (Bisa diakses Admin & Pemilik)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup rute yang memerlukan login (auth)
Route::middleware('auth')->group(function () {
    
    // --- MANAJEMEN PROFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MANAJEMEN DATA MAKANAN/JAJANAN ---
    Route::resource('makanan', MakananController::class);
    // API untuk scan barcode (digunakan di halaman mutasi)
    Route::get('/api/makanan/find-by-barcode/{barcode}', [MakananController::class, 'findByBarcode']);

    // --- MUTASI BARANG MASUK (Admin & Pemilik) ---
    Route::prefix('mutasi-masuk')->group(function () {
        // Halaman Laporan Barang Masuk
        Route::get('/', [MutasiMasukController::class, 'index'])->name('mutasi_masuk.index');
        // Form Tambah Barang Masuk
        Route::get('/tambah', [MutasiMasukController::class, 'create'])->name('log.create');
        // Proses Simpan Barang Masuk
        Route::post('/tambah', [MutasiMasukController::class, 'store'])->name('log.store');
    });

    // --- MUTASI BARANG KELUAR (Khusus Pemilik) ---
    // Menggunakan middleware 'role:Pemilik' untuk keamanan tambahan
    Route::middleware('role:Pemilik')->prefix('mutasi-keluar')->group(function () {
        // Halaman Laporan Barang Keluar
        Route::get('/', [MutasiKeluarController::class, 'index'])->name('mutasi_keluar.index');
        // Form Tambah Barang Keluar
        Route::get('/tambah', [MutasiKeluarController::class, 'create'])->name('log.keluar.create');
        // Proses Simpan Barang Keluar
        Route::post('/tambah', [MutasiKeluarController::class, 'store'])->name('log.keluar.store');
    });

});

// Memuat rute autentikasi bawaan Laravel Breeze/Jetstream
require __DIR__.'/auth.php';