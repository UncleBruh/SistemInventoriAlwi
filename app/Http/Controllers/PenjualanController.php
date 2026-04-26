<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MutasiKeluar;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data MutasiKeluar yang hanya untuk Penjualan
        $query = MutasiKeluar::with(['makanan', 'pengguna'])
            ->where('alasan', 'Penjualan')
            ->latest('tgl_mutasi');

        // Filter berdasarkan tanggal jika ada
        if ($request->filled('tanggal_mulai')) {
            $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
            $query->whereDate('tgl_mutasi', '>=', $tanggalMulai);
        }

        if ($request->filled('tanggal_akhir')) {
            $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->endOfDay();
            $query->whereDate('tgl_mutasi', '<=', $tanggalAkhir);
        }

        $allData = $query->get();

        // Hitung ringkasan data
        $totalPendapatan = $allData->sum(function($item) {
            return $item->jumlah_keluar * $item->makanan->harga;
        });

        $jumlahTransaksi = $allData->count();
        $totalUnitTerjual = $allData->sum('jumlah_keluar');

        // Kelompokkan data berdasarkan tanggal untuk laporan per hari
        $laporanPerHariAll = $allData->groupBy(function($item) {
            return Carbon::parse($item->tgl_mutasi)->format('Y-m-d');
        })->map(function($items) {
            $totalHariIni = $items->sum(function($item) {
                return $item->jumlah_keluar * $item->makanan->harga;
            });

            return [
                'tanggal' => $items->first()->tgl_mutasi,
                'items' => $items,
                'total' => $totalHariIni,
                'jumlah_unit' => $items->sum('jumlah_keluar'),
            ];
        })->sortByDesc('tanggal')->values();

        // Manual pagination untuk laporan per hari (15 hari per halaman)
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $total = $laporanPerHariAll->count();
        $offset = ($currentPage - 1) * $perPage;

        $laporanPerHari = new LengthAwarePaginator(
            $laporanPerHariAll->slice($offset, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('penjualan.index', compact('laporanPerHari', 'totalPendapatan', 'jumlahTransaksi', 'totalUnitTerjual'));
    }
}

