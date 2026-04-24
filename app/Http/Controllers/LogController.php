<?php

namespace App\Http\Controllers;

use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
use Carbon\Carbon;

class LogController extends Controller
{
    public function index()
    {
        $masuk = MutasiMasuk::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'tgl_input' => $item->created_at->format('Y-m-d H:i:s'), // Kapan diklik simpan
                'tgl_aktual' => Carbon::parse($item->tgl_mutasi)->format('Y-m-d'), // Kapan fisik barang masuk
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Masuk',
                'jumlah' => '+' . $item->jumlah_masuk,
                'alasan' => '-',
                'petugas' => $item->pengguna->username,
                'sort_date' => $item->created_at // Acuan pengurutan terbaru
            ];
        });

        $keluar = MutasiKeluar::with(['makanan', 'pengguna'])->get()->map(function($item) {
            return (object) [
                'tgl_input' => $item->created_at->format('Y-m-d H:i:s'),
                'tgl_aktual' => Carbon::parse($item->tgl_mutasi)->format('Y-m-d'),
                'nama_makanan' => $item->makanan->nama_makanan,
                'jenis' => 'Barang Keluar',
                'jumlah' => '-' . $item->jumlah_keluar,
                'alasan' => $item->alasan,
                'petugas' => $item->pengguna->username,
                'sort_date' => $item->created_at
            ];
        });

        // Gabungkan dan urutkan dari aktivitas input terbaru
        $semua_log = $masuk->concat($keluar)->sortByDesc('sort_date')->values();

        return view('log.index', compact('semua_log'));
    }
}