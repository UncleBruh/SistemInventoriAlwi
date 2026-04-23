<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakananController;
use App\Http\Controllers\MutasiMasukController;
use App\Http\Controllers\MutasiKeluarController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

// 1. Pengalihan Halaman Utama
Route::redirect('/', '/login');

// 2. Dashboard (Akses: Admin & Pemilik)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Grup Rute yang Memerlukan Login
Route::middleware('auth')->group(function () {
    
    // --- MANAJEMEN PROFIL ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // --- MANAJEMEN DATA MAKANAN ---
    Route::resource('makanan', MakananController::class);
    // API scan barcode untuk fitur pencarian otomatis di form mutasi
    Route::get('/api/makanan/find-by-barcode/{barcode}', [MakananController::class, 'findByBarcode']);

    // --- MUTASI BARANG MASUK (Akses: Admin & Pemilik) ---
    Route::prefix('mutasi-masuk')->group(function () {
        // Halaman Riwayat Masuk
        Route::get('/', [MutasiMasukController::class, 'index'])->name('mutasi_masuk.index');
        // Halaman Form Tambah Masuk
        Route::get('/tambah', [MutasiMasukController::class, 'create'])->name('log.create');
        // Proses Simpan Data Masuk
        Route::post('/tambah', [MutasiMasukController::class, 'store'])->name('log.store');
    });

    // --- FITUR KHUSUS PEMILIK (Akses: Hanya Pemilik) ---
    // Menggunakan middleware 'role:Pemilik' untuk keamanan sistem
    Route::middleware('role:Pemilik')->group(function () {
        
        // Laporan Log Aktivitas Gabungan (Masuk & Keluar)
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log.aktivitas');

        // Mutasi Barang Keluar
        Route::prefix('mutasi-keluar')->group(function () {
            // Halaman Riwayat Keluar
            Route::get('/', [MutasiKeluarController::class, 'index'])->name('mutasi_keluar.index');
            // Halaman Form Tambah Keluar
            Route::get('/tambah', [MutasiKeluarController::class, 'create'])->name('log.keluar.create');
            // Proses Simpan Data Keluar
            Route::post('/tambah', [MutasiKeluarController::class, 'store'])->name('log.keluar.store');
        });
    });
});

// 4. Memuat Rute Autentikasi Bawaan (Login, Register, dll)
require __DIR__.'/auth.php';