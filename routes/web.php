<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MakananController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Grup untuk user yang sudah login (Admin & Pemilik)
Route::middleware('auth')->group(function () {
    
    // Rute Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Rute Makanan (CRUD Jajanan) - Bisa diakses keduanya
    Route::resource('makanan', MakananController::class);

    // API endpoint untuk scan barcode
    Route::get('/api/makanan/find-by-barcode/{barcode}', [MakananController::class, 'findByBarcode']);

    // --- BAGIAN MUTASI BARANG ---

    // 1. Barang Masuk (Admin & Pemilik bisa akses)
    Route::get('/barang-masuk', [LogController::class, 'create'])->name('log.create');
    Route::post('/barang-masuk', [LogController::class, 'store'])->name('log.store');

    // 2. Fitur Khusus Pemilik (Barang Keluar & Laporan)
    Route::middleware('role:Pemilik')->group(function () {
        // Halaman Riwayat/Laporan Mutasi
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log.aktivitas');
        
        // Halaman Barang Keluar (Hanya Pemilik yang bisa buka form & simpan)
        Route::get('/barang-keluar', [LogController::class, 'create'])->name('log.keluar.create');
        // Catatan: store tetap diarahkan ke LogController@store yang sudah kita beri validasi role di dalamnya
    });
});

require __DIR__.'/auth.php';