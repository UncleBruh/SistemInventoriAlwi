<?php

namespace App\Http\Controllers;

use App\Models\MutasiMasuk;
use App\Models\MutasiKeluar;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
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
        $semua_log_all = $masuk->concat($keluar)->sortByDesc('sort_date')->values();

        // Manual pagination (50 item per halaman)
        $perPage = 50;
        $currentPage = $request->get('page', 1);
        $total = $semua_log_all->count();
        $offset = ($currentPage - 1) * $perPage;

        $semua_log = new LengthAwarePaginator(
            $semua_log_all->slice($offset, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('log.index', compact('semua_log'));
    }
}
