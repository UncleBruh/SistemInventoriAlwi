<?php

namespace App\Http\Controllers;

use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;

class LogController extends Controller
{
    public function index()
    {
        // 1. Ambil data masuk dan format
        $masuk = MutasiMasuk::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'tgl_mutasi' => $item->tgl_mutasi,
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Masuk',
                'jumlah' => '+' . $item->jumlah_masuk,
                'alasan' => '-', // Mutasi masuk tidak memiliki alasan
                'petugas' => $item->pengguna->username,
            ];
        });

        // 2. Ambil data keluar dan format
        $keluar = MutasiKeluar::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'tgl_mutasi' => $item->tgl_mutasi,
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Keluar',
                'jumlah' => '-' . $item->jumlah_keluar,
                'alasan' => $item->alasan,
                'petugas' => $item->pengguna->username,
            ];
        });

        // 3. Gabungkan kedua data (concat) lalu urutkan dari tanggal terbaru (sortByDesc)
        $semua_log = $masuk->concat($keluar)->sortByDesc('tgl_mutasi')->values();

        return view('log.index', compact('semua_log'));
    }
}