<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakananController;
use App\Http\Controllers\MutasiMasukController;
use App\Http\Controllers\MutasiKeluarController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    
    // Profil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Database Jajanan
    Route::resource('makanan', MakananController::class);
    Route::get('/api/makanan/find-by-barcode/{barcode}', [MakananController::class, 'findByBarcode']);

    // Mutasi Masuk (Admin & Pemilik)
    Route::prefix('mutasi-masuk')->group(function () {
        Route::get('/', [MutasiMasukController::class, 'index'])->name('mutasi_masuk.index');
        Route::get('/tambah', [MutasiMasukController::class, 'create'])->name('mutasi_masuk.create');
        Route::post('/tambah', [MutasiMasukController::class, 'store'])->name('mutasi_masuk.store');
    });

    // Mutasi Keluar & Log Gabungan (Hanya Pemilik)
    Route::middleware('role:Pemilik')->group(function () {
        
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log.aktivitas');

        Route::prefix('mutasi-keluar')->group(function () {
            Route::get('/', [MutasiKeluarController::class, 'index'])->name('mutasi_keluar.index');
            Route::get('/tambah', [MutasiKeluarController::class, 'create'])->name('mutasi_keluar.create');
            Route::post('/tambah', [MutasiKeluarController::class, 'store'])->name('mutasi_keluar.store');
        });
    });
});

require __DIR__.'/auth.php';