<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MakananController;
use App\Http\Controllers\MutasiMasukController;
use App\Http\Controllers\MutasiKeluarController;
use App\Http\Controllers\AlokasiGudangEtalaseController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

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

    Route::middleware('role:Pemilik,Admin')->group(function () {
        Route::prefix('mutasi-keluar')->group(function () {
            Route::get('/', [MutasiKeluarController::class, 'index'])->name('mutasi_keluar.index');
            Route::get('/tambah', [MutasiKeluarController::class, 'create'])->name('mutasi_keluar.create');
            Route::post('/tambah', [MutasiKeluarController::class, 'store'])->name('mutasi_keluar.store');
        });

        // Alokasi Gudang ke Etalase (Pemilik/Admin - bisa buat, lihat, tapi hanya Pemilik bisa hapus)
        Route::prefix('alokasi-gudang-etalase')->group(function () {
            Route::get('/', [AlokasiGudangEtalaseController::class, 'index'])->name('alokasi-gudang-etalase.index');
            Route::get('/tambah', [AlokasiGudangEtalaseController::class, 'create'])->name('alokasi-gudang-etalase.create');
            Route::post('/tambah', [AlokasiGudangEtalaseController::class, 'store'])->name('alokasi-gudang-etalase.store');
            Route::get('/{id}', [AlokasiGudangEtalaseController::class, 'show'])->name('alokasi-gudang-etalase.show');
        });
    });

    // Laporan Penjualan & Hapus Alokasi (Hanya Pemilik)
    Route::middleware('role:Pemilik')->group(function () {
        Route::delete('/alokasi-gudang-etalase/{id}', [AlokasiGudangEtalaseController::class, 'destroy'])->name('alokasi-gudang-etalase.destroy');
        Route::get('/penjualan', [PenjualanController::class, 'index'])->name('penjualan.index');

        // Log Aktivitas (Hanya Pemilik sahaja)
        Route::get('/log-aktivitas', [LogController::class, 'index'])->name('log.aktivitas');
    });

    // --- MANAJEMEN KATEGORI ---
    Route::resource('kategori', KategoriController::class)->only(['index', 'store', 'destroy']);

    // Rute Laporan
Route::get('/laporan/masuk', [LaporanController::class, 'mutasiMasuk'])->name('laporan.masuk');
Route::get('/laporan/masuk/pdf', [LaporanController::class, 'cetakMutasiMasuk'])->name('laporan.masuk.pdf');

Route::get('/laporan/keluar', [LaporanController::class, 'mutasiKeluar'])->name('laporan.keluar');
Route::get('/laporan/keluar/pdf', [LaporanController::class, 'cetakMutasiKeluar'])->name('laporan.keluar.pdf');
});


require __DIR__.'/auth.php';
