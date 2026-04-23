<?php

namespace App\Http\Controllers;

use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;

class LogController extends Controller
{
    /**
     * Menampilkan riwayat mutasi (Hanya untuk Pemilik).
     */
    public function index()
    {
        $masuk = MutasiMasuk::with(['makanan', 'pengguna'])->latest()->get();
        $keluar = MutasiKeluar::with(['makanan', 'pengguna'])->latest()->get();

        return view('log.index', compact('masuk', 'keluar'));
    }
}